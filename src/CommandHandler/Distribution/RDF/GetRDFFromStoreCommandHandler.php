<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\GetRDFFromStoreCommand;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\DataSpecification\DataModel\NamespacePrefix;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Service\Distribution\MysqlBasedDistributionService;
use App\Service\Distribution\TripleStoreBasedDistributionService;
use App\Service\EncryptionService;
use App\Service\UriHelper;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetRDFFromStoreCommandHandler
{
    public function __construct(private MysqlBasedDistributionService $mysqlBasedDistributionService, private TripleStoreBasedDistributionService $tripleStoreBasedDistributionService, private UriHelper $uriHelper, private EncryptionService $encryptionService, private Security $security)
    {
    }

    /** @throws Exception */
    public function __invoke(GetRDFFromStoreCommand $command): string
    {
        $distribution = $command->getDistribution()->getDistribution();

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $rdfDistribution = $distribution->getContents();
        assert($rdfDistribution instanceof RDFDistribution);

        $namedGraphUrl = $command->getRecord() !== null ? $this->uriHelper->getUri($command->getDistribution()) . '/g/' . $command->getRecord() : null;

        if ($rdfDistribution->getDatabaseType()->isStardog()) {
            return $this->tripleStoreBasedDistributionService->getDataFromStore(
                $distribution->getDatabaseInformation(),
                $this->encryptionService,
                $namedGraphUrl
            );
        }

        if ($rdfDistribution->getDatabaseType()->isMysql()) {
            $dataModel = $command->getDistribution()->getCurrentDataModelVersion();
            $prefixes = $dataModel->getPrefixes();
            $nameSpaces = [];

            foreach ($prefixes as $prefix) {
                /** @var NamespacePrefix $prefix */
                $nameSpaces[$prefix->getPrefix()] = $prefix->getUri()->getValue();
            }

            return $this->mysqlBasedDistributionService->getDataFromStore(
                $distribution->getDatabaseInformation(),
                $this->encryptionService,
                $namedGraphUrl,
                $nameSpaces
            );
        }

        throw new NotFound();
    }
}
