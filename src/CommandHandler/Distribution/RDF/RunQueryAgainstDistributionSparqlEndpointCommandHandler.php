<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\RunQueryAgainstDistributionSparqlEndpointCommand;
use App\Exception\NoAccessPermission;
use App\Graph\SparqlResponse;
use App\Service\EncryptionService;
use App\Service\TripleStoreBasedDistributionService;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class RunQueryAgainstDistributionSparqlEndpointCommandHandler implements MessageHandlerInterface
{
    private TripleStoreBasedDistributionService $distributionService;

    private EncryptionService $encryptionService;

    private Security $security;

    public function __construct(TripleStoreBasedDistributionService $distributionService, EncryptionService $encryptionService, Security $security)
    {
        $this->distributionService = $distributionService;
        $this->encryptionService = $encryptionService;
        $this->security = $security;
    }

    /** @throws Exception */
    public function __invoke(RunQueryAgainstDistributionSparqlEndpointCommand $command): SparqlResponse
    {
        $distribution = $command->getDistribution()->getDistribution();

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        return $this->distributionService->runQuery(
            $command->getQuery(),
            $distribution->getDatabaseInformation(),
            $this->encryptionService
        );
    }
}
