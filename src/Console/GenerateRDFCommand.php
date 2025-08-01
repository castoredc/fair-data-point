<?php
/** @phpcs:disable SlevomatCodingStandard.TypeHints.PropertyTypeHint.MissingAnyTypeHint */
declare(strict_types=1);

namespace App\Console;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\Data\Log\DistributionGenerationRecordLog;
use App\Entity\Enum\CastorEntityType;
use App\Entity\Enum\DistributionGenerationStatus;
use App\Model\Castor\ApiClient;
use App\Service\CastorEntityHelper;
use App\Service\DataTransformationService;
use App\Service\Distribution\MysqlBasedDistributionService;
use App\Service\Distribution\TripleStoreBasedDistributionService;
use App\Service\EncryptionService;
use App\Service\RDF\RenderRdfDataHelper;
use App\Service\UriHelper;
use Doctrine\ORM\EntityManagerInterface;
use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use function assert;
use function count;
use function sprintf;
use const DATE_ATOM;

#[AsCommand(name: 'app:generate-rdf')]
class GenerateRDFCommand extends Command
{
    public function __construct(
        private ApiClient $apiClient,
        private EntityManagerInterface $em,
        private CastorEntityHelper $entityHelper,
        private UriHelper $uriHelper,
        private MysqlBasedDistributionService $mysqlBasedDistributionService,
        private TripleStoreBasedDistributionService $tripleStoreBasedDistributionService,
        private EncryptionService $encryptionService,
        private LoggerInterface $logger,
        private DataTransformationService $dataTransformationService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Generates and stores RDF of cached distributions')
            ->addOption(
                'force',
                null,
                InputOption::VALUE_OPTIONAL,
                'Force update',
                false
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln(
            [
                'RDF Generator',
                '============',
                '',
            ]
        );

        $forceUpdate = ($input->getOption('force') !== false);

        if ($forceUpdate) {
            $output->writeln('FORCE UPDATE');
        }

        /** @var RDFDistribution[] $rdfDistributionContents */
        $rdfDistributionContents = $this->em->getRepository(RDFDistribution::class)->findBy(['isCached' => true]);

        $studies = [];
        $studyRecordData = [];
        $studyOptionGroups = [];

        $output->writeln('');
        $output->writeln('======');
        $output->writeln('Setting up');
        $output->writeln('======');
        $output->writeln('');

        foreach ($rdfDistributionContents as $rdfDistributionContent) {
            $distribution = $rdfDistributionContent->getDistribution();

            if ($distribution->isArchived()) {
                continue;
            }

            $dbStudy = $distribution->getDataset()->getStudy();
            assert($dbStudy instanceof CastorStudy);

            $apiUser = $distribution->getApiUser();
            $this->apiClient->useApiUser($apiUser, $this->encryptionService);

            if (isset($studyRecordData[$dbStudy->getId()])) {
                continue;
            }

            $output->writeln('- Getting study (meta)data for ' . $dbStudy->getName() . ' <' . $dbStudy->getId() . '>');

            // Cache study information
            $study = $this->apiClient->getStudy($dbStudy->getSourceId());
            $output->writeln('  - Study information and records');

            $studies[$dbStudy->getId()] = $study;
            $studyOptionGroups[$dbStudy->getId()] = $this->entityHelper->getEntitiesByType($dbStudy, CastorEntityType::fieldOptionGroup());
            $output->writeln('  - Option groups');

            /** @var Record[] $records */
            $records = $this->entityHelper->getRecords($dbStudy)->toArray();
            $output->writeln('  - Record data');

            $studyRecordData[$dbStudy->getId()] = [];

            foreach ($records as $record) {
                $studyRecordData[$dbStudy->getId()][$record->getId()] = $this->apiClient->getRecordDataCollection($study, $record);
            }
        }

        foreach ($rdfDistributionContents as $rdfDistributionContent) {
            $distribution = $rdfDistributionContent->getDistribution();

            if ($distribution->isArchived()) {
                continue;
            }

            $log = new DistributionGenerationLog($rdfDistributionContent);

            $output->writeln('');
            $output->writeln('======');
            $output->writeln('Distribution ' . $distribution->getSlug());
            $output->writeln('======');
            $output->writeln('');

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

            $dbStudy = $distribution->getDataset()->getStudy();
            $study = $studies[$dbStudy->getId()];
            $optionGroups = $studyOptionGroups[$dbStudy->getId()];

            /** @var Record[] $records */
            $records = $studyRecordData[$dbStudy->getId()];

            $distributionUri = $this->uriHelper->getUri($rdfDistributionContent);
            $graphUri = $distributionUri . '/g';

            $output->writeln(sprintf("Last import: \t %s", $lastImport?->format(DATE_ATOM) ?? 'Never'));
            $output->writeln(sprintf("URI: \t\t %s", $distributionUri));
            $output->writeln(sprintf("API user: \t <%s>", $apiUser->getEmailAddress()));

            if ($rdfDistributionContent->getDatabaseType()->isStardog()) {
                $this->tripleStoreBasedDistributionService->createDatabaseApiClient($dbInformation, $this->encryptionService);
                $output->writeln(sprintf("Stardog DB: \t %s", $dbInformation->getDatabase()));
            } elseif ($rdfDistributionContent->getDatabaseType()->isMysql()) {
                $store = $this->mysqlBasedDistributionService->duplicateArc2Store($dbInformation, $this->encryptionService);
                $output->writeln(sprintf("RDF Store: \t %s", $store->getName()));
            }

            $output->writeln(sprintf("Records found: \t %s record(s)", count($records)));
            $output->writeln('');

            $output->writeln('- Setting up RenderRdfDataHelper');
            $output->writeln('  - Study: ' . $study->getName() . ' <' . $study->getId() . '>');
            $output->writeln('  - Option groups: ' . count($optionGroups));

            $helper = new RenderRdfDataHelper($distribution, $this->apiClient, $this->entityHelper, $this->uriHelper, $this->dataTransformationService, $study, $optionGroups);
            $output->writeln('');

            $recordsSubset = $helper->getSubset($records);
            $output->writeln(sprintf("Subset: \t %s record(s)", count($recordsSubset)));

            $dataModel = $rdfDistributionContent->getCurrentDataModelVersion();
            $prefixes = $dataModel->getPrefixes();

            foreach ($prefixes as $prefix) {
                RdfNamespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
            }

            if ($rdfDistributionContent->getDatabaseType()->isStardog()) {
                $this->tripleStoreBasedDistributionService->importNamespaces(RdfNamespace::namespaces());
            }

            $imported = [];
            $errors = [];
            $skipped = [];

            foreach ($recordsSubset as $record) {
                $recordLog = new DistributionGenerationRecordLog($record);

                $import = false;
                $recordGraphUri = $graphUri . '/' . $record->getId();

                if ($lastImport === null) {
                    $output->writeln(sprintf('- Importing record %s', $record->getId()));
                    $import = true;
                } elseif ($forceUpdate === true) {
                    $output->writeln(sprintf('- Forced importing record %s', $record->getId()));
                    $import = true;
                } elseif ($record->getCreatedOn() > $lastImport) {
                    $output->writeln(sprintf('- Record %s is created (%s) since last import (%s)', $record->getId(), $record->getCreatedOn()->format(DATE_ATOM), $lastImport->format(DATE_ATOM)));
                    $import = true;
                } elseif ($record->getUpdatedOn() > $lastImport) {
                    $output->writeln(sprintf('- Record %s is changed since last import, old render will be removed', $record->getId()));
                    $import = true;
                } else {
                    $output->writeln(sprintf('- Record %s is not changed since last import', $record->getId()));
                }

                if ($import) {
                    try {
                        $graph = new Graph();

                        $output->writeln('    - Rendering record ' . $record->getId());

                        $graph = $helper->renderRecord($record, $graph);

                        $output->writeln(sprintf('    - Saving record to <%s>', $recordGraphUri));

                        if ($rdfDistributionContent->getDatabaseType()->isStardog()) {
                            $this->tripleStoreBasedDistributionService->addDataToStore($graph, $recordGraphUri);
                        } elseif ($rdfDistributionContent->getDatabaseType()->isMysql()) {
                            $this->mysqlBasedDistributionService->addDataToStore(
                                $dbInformation,
                                $this->encryptionService,
                                $graph,
                                $recordGraphUri
                            );
                        }

                        $imported[] = $record;
                        $recordLog->setStatus(DistributionGenerationStatus::success());
                    } catch (Throwable $t) {
                        $output->writeln('    - An error occurred while rendering the record:');
                        $output->writeln('      ' . $t::class);

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
                                'exception' => $t::class,
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

            if ($rdfDistributionContent->getDatabaseType()->isMysql() && count($imported) > 0) {
                try {
                    $this->mysqlBasedDistributionService->optimizeStore(
                        $dbInformation,
                        $this->encryptionService
                    );
                } catch (Throwable $t) {
                    $output->writeln('    - An error occurred while optimizing the triple store:');
                    $output->writeln('      ' . $t::class);

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
                            'exception' => $t::class,
                            'message' => $t->getMessage(),
                        ]
                    );
                }
            }

            $this->em->persist($log);

            $this->em->persist($rdfDistributionContent);
            $this->em->flush();
        }

        return Command::SUCCESS;
    }
}
