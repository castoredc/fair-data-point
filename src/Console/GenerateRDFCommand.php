<?php
declare(strict_types=1);

namespace App\Console;

use App\Connection\DistributionService;
use App\Encryption\EncryptionService;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Data\RDF\RDFDistribution;
use App\Model\Castor\ApiClient;
use App\Service\CastorEntityHelper;
use App\Service\RDFRenderHelper;
use App\Service\UriHelper;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyRdf_Graph;
use EasyRdf_Namespace;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use const DATE_ATOM;
use function assert;
use function count;
use function sprintf;

class GenerateRDFCommand extends Command
{
    /** @var string */
    protected static $defaultName = 'app:generate-rdf';

    /** @var ApiClient */
    private $apiClient;

    /** @var EntityManagerInterface */
    private $em;

    /** @var CastorEntityHelper */
    private $entityHelper;

    /** @var UriHelper */
    private $uriHelper;

    /** @var DistributionService */
    private $distributionService;

    /** @var EncryptionService */
    private $encryptionService;

    public function __construct(ApiClient $apiClient, EntityManagerInterface $em, CastorEntityHelper $entityHelper, UriHelper $uriHelper, DistributionService $distributionService, EncryptionService $encryptionService)
    {
        $this->apiClient = $apiClient;
        $this->em = $em;
        $this->entityHelper = $entityHelper;
        $this->uriHelper = $uriHelper;
        $this->distributionService = $distributionService;
        $this->encryptionService = $encryptionService;

        parent::__construct();
    }

    /** @inheritDoc */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'RDF Generator',
            '============',
            '',
        ]);

        /** @var RDFDistribution[] $rdfDistributionContents */
        $rdfDistributionContents = $this->em->getRepository(RDFDistribution::class)->findBy(['isCached' => true]);

        foreach ($rdfDistributionContents as $rdfDistributionContent) {
            $distribution = $rdfDistributionContent->getDistribution();

            $output->writeln('== ' . $distribution->getSlug() . ' ==');

            $lastImport = $rdfDistributionContent->getLastImport();

            $dbInformation = $distribution->getDatabaseInformation();
            $apiUser = $distribution->getApiUser();
            $this->apiClient->useApiUser($apiUser, $this->encryptionService);
            $this->entityHelper->useApiUser($apiUser);

            $study = $distribution->getDataset()->getStudy();
            assert($study instanceof CastorStudy);

            /** @var Record[] $records */
            $records = $this->apiClient->getRecords($study)->toArray();

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

            $dataModel = $rdfDistributionContent->getDataModel();
            $prefixes = $dataModel->getPrefixes();

            foreach ($prefixes as $prefix) {
                /** @var NamespacePrefix $prefix */
                EasyRdf_Namespace::set($prefix->getPrefix(), $prefix->getUri()->getValue());
            }

            $imported = [];
            $notImported = [];

            foreach ($records as $record) {
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
                    $graph = new EasyRdf_Graph();

                    $output->writeln('    - Rendering record ' . $record->getId());

                    $graph = $helper->renderRecord($record, $graph);
                    $turtle = $graph->serialise('turtle');

                    $output->writeln(sprintf('    - Saving record to <%s>', $recordGraphUri));

                    $store->insert($turtle, $recordGraphUri);

                    $imported[] = $record;
                } else {
                    $notImported[] = $record;
                }
            }

            $output->writeln(['', 'Import finished']);
            $output->writeln(sprintf('- %s record(s) imported', count($imported)));
            $output->writeln(sprintf('- %s record(s) skipped', count($notImported)));

            $rdfDistributionContent->setLastImport($timeStamp);
            $this->em->persist($rdfDistributionContent);
            $this->em->flush();

            $store->optimizeTables();
        }

        return 0;
    }
}
