<?php
/**
 * @phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint
 */
declare(strict_types=1);

namespace App\Console;

use App\Connection\DistributionService;
use App\Encryption\EncryptionService;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\Data\Log\DistributionGenerationRecordLog;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\Enum\DistributionGenerationStatus;
use App\Model\Castor\ApiClient;
use App\Service\CastorEntityHelper;
use App\Service\RDFRenderHelper;
use App\Service\UriHelper;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyRdf_Graph;
use EasyRdf_Namespace;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function assert;
use function count;
use function get_class;
use function sprintf;
use const DATE_ATOM;

class GenerateRDFCommand extends Command
{
    /** @phpcs:ignore */
    protected static $defaultName = 'app:generate-rdf';

    private ApiClient $apiClient;
    private EntityManagerInterface $em;
    private CastorEntityHelper $entityHelper;
    private UriHelper $uriHelper;
    private DistributionService $distributionService;
    private EncryptionService $encryptionService;
    private LoggerInterface $logger;

    public function __construct(
        ApiClient $apiClient,
        EntityManagerInterface $em,
        CastorEntityHelper $entityHelper,
        UriHelper $uriHelper,
        DistributionService $distributionService,
        EncryptionService $encryptionService,
        LoggerInterface $logger
    ) {
        $this->apiClient = $apiClient;
        $this->em = $em;
        $this->entityHelper = $entityHelper;
        $this->uriHelper = $uriHelper;
        $this->distributionService = $distributionService;
        $this->encryptionService = $encryptionService;
        $this->logger = $logger;

        parent::__construct();
    }

    /** @inheritDoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(
            [
                'RDF Generator',
                '============',
                '',
            ]
        );

        /** @var RDFDistribution[] $rdfDistributionContents */
        $rdfDistributionContents = $this->em->getRepository(RDFDistribution::class)->findBy(['isCached' => true]);

        foreach ($rdfDistributionContents as $rdfDistributionContent) {
            $distribution = $rdfDistributionContent->getDistribution();
            $log = new DistributionGenerationLog($rdfDistributionContent);

            $output->writeln('== ' . $distribution->getSlug() . ' ==');

            $lastImport = $rdfDistributionContent->getLastGenerationDate();

            $dbInformation = $distribution->getDatabaseInformation();
            $apiUser = $distribution->getApiUser();

            if ($apiUser === null) {
                $log->setStatus(DistributionGenerationStatus::error());

                $log->addError(
                    ['message' => 'There was no API user assigned to this distribution.']
                );

                $this->em->persist($log);
                $this->em->flush();
                continue;
            }

            $this->apiClient->useApiUser($apiUser, $this->encryptionService);
            $this->entityHelper->useApiUser($apiUser);

            $study = $distribution->getDataset()->getStudy();
            assert($study instanceof CastorStudy);

            /** @var Record[] $records */
            $records = $this->entityHelper->getRecords($study)->toArray();

            $distributionUri = $this->uriHelper->getUri($rdfDistributionContent);
            $graphUri = $distributionUri . '/g';
            $timeStamp = new DateTimeImmutable();

            $store = $this->distributionService->duplicateArc2Store($dbInformation, $this->encryptionService);

            $output->writeln(sprintf("Last import: \t %s", $lastImport !== null ? $lastImport->format(DATE_ATOM) : 'Never'));
            $output->writeln(sprintf("URI: \t\t %s", $distributionUri));
            $output->writeln(sprintf("API user: \t <%s>", $apiUser->getEmailAddress()));
            $output->writeln(sprintf("RDF Store: \t %s", $store->getName()));
            $output->writeln(sprintf("Records found: \t %s record(s)", count($records)));
            $output->writeln('');
            $helper = new RDFRenderHelper($distribution, $this->apiClient, $this->entityHelper, $this->uriHelper);

            $dataModel = $rdfDistributionContent->getCurrentDataModelVersion();
            $prefixes = $dataModel->getPrefixes();

            foreach ($prefixes as $prefix) {
                EasyRdf_Namespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
            }

            $imported = [];
            $errors = [];
            $skipped = [];

            foreach ($records as $record) {
                $recordLog = new DistributionGenerationRecordLog($record);

                $import = false;
                $recordGraphUri = $graphUri . '/' . $record->getId();

                if ($lastImport === null) {
                    $output->writeln(sprintf('- Importing record %s', $record->getId()));
                    $import = true;
                } elseif ($record->getCreatedOn() > $lastImport) {
                    $output->writeln(sprintf('- Record %s is created since last import', $record->getId()));
                    $import = true;
                } elseif ($record->getUpdatedOn() > $lastImport) {
                    $output->writeln(sprintf('- Record %s is changed since last import', $record->getId()));
                    $import = true;

                    $output->writeln('    - Removing old render for record ' . $record->getId());
                    $store->delete(false, $recordGraphUri);
                } else {
                    $output->writeln(sprintf('- Record %s is not changed since last import', $record->getId()));
                }

                if ($import) {
                    try {
                        $graph = new EasyRdf_Graph();

                        $output->writeln('    - Rendering record ' . $record->getId());

                        $graph = $helper->renderRecord($record, $graph);
                        $turtle = $graph->serialise('turtle');

                        $output->writeln(sprintf('    - Saving record to <%s>', $recordGraphUri));

                        $store->insert($turtle, $recordGraphUri);

                        $imported[] = $record;
                        $recordLog->setStatus(DistributionGenerationStatus::success());
                    } catch (Throwable $t) {
                        $output->writeln('    - An error occurred while rendering the record:');
                        $output->writeln('      ' . get_class($t));

                        if ($t->getMessage() !== '') {
                            $output->writeln('      ' . $t->getMessage());
                        }

                        $this->logger->critical(
                            'An error occurred while rendering the record',
                            [
                                'exception' => $t,
                                'Message' => $t->getMessage(),
                                'Distribution' => $distribution->getSlug(),
                                'DistributionID' => $distribution->getId(),
                                'RecordID' => $record->getId(),
                            ]
                        );

                        $errors[] = $record;

                        $recordLog->setStatus(DistributionGenerationStatus::error());

                        $recordLog->addError(
                            [
                                'exception' => get_class($t),
                                'message' => $t->getMessage(),
                            ]
                        );
                    }
                } else {
                    $skipped[] = $record;

                    $recordLog->setStatus(DistributionGenerationStatus::notUpdated());
                }

                $log->addRecord($recordLog);
                $this->em->persist($recordLog);
            }

            $output->writeln(['', 'Import finished']);
            $output->writeln(sprintf('- %s record(s) imported', count($imported)));
            $output->writeln(sprintf('- %s record(s) not imported due to errors', count($errors)));
            $output->writeln(sprintf('- %s record(s) skipped', count($skipped)));

            if (count($imported) > 0 && count($errors) > 0) {
                $log->setStatus(DistributionGenerationStatus::partially());
            } elseif (count($errors) > 0) {
                $log->setStatus(DistributionGenerationStatus::error());
            } elseif (count($imported) > 0) {
                $log->setStatus(DistributionGenerationStatus::success());
            } else {
                $log->setStatus(DistributionGenerationStatus::notUpdated());
            }

            if (count($imported) > 0) {
                try {
                    $store->optimizeTables();
                } catch (Throwable $t) {
                    $output->writeln('    - An error occurred while optimizing the triple store:');
                    $output->writeln('      ' . get_class($t));

                    if ($t->getMessage() !== '') {
                        $output->writeln('      ' . $t->getMessage());
                    }

                    $this->logger->critical(
                        'An error occurred while optimizing the triple store',
                        [
                            'exception' => $t,
                            'Message' => $t->getMessage(),
                            'Distribution' => $distribution->getSlug(),
                            'DistributionID' => $distribution->getId(),
                        ]
                    );

                    $log->addError(
                        [
                            'exception' => get_class($t),
                            'message' => $t->getMessage(),
                        ]
                    );
                }
            }

            $this->em->persist($log);

            $this->em->persist($rdfDistributionContent);
            $this->em->flush();
        }

        return 0;
    }
}
