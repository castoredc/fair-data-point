<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\RunQueryAgainstDistributionSparqlEndpointCommand;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Graph\SparqlResponse;
use App\Service\Distribution\MysqlBasedDistributionService;
use App\Service\Distribution\TripleStoreBasedDistributionService;
use App\Service\EncryptionService;
use Exception;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class RunQueryAgainstDistributionSparqlEndpointCommandHandler
{
    public function __construct(
        protected MysqlBasedDistributionService $mysqlBasedDistributionService,
        protected TripleStoreBasedDistributionService $tripleStoreBasedDistributionService,
        private EncryptionService $encryptionService,
        private Security $security,
    ) {
    }

    /** @throws Exception */
    public function __invoke(RunQueryAgainstDistributionSparqlEndpointCommand $command): SparqlResponse
    {
        $distribution = $command->getDistribution()->getDistribution();

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $rdfDistribution = $distribution->getContents();
        assert($rdfDistribution instanceof RDFDistribution);

        if ($rdfDistribution->getDatabaseType()->isStardog()) {
            return $this->tripleStoreBasedDistributionService->runQuery(
                $command->getQuery(),
                $distribution->getDatabaseInformation(),
                $this->encryptionService
            );
        }

        if ($rdfDistribution->getDatabaseType()->isMysql()) {
            return $this->mysqlBasedDistributionService->runQuery(
                $command->getQuery(),
                $distribution->getDatabaseInformation(),
                $this->encryptionService
            );
        }

        throw new NotFound();
    }
}
