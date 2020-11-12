<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution\RDF;

use App\Command\Distribution\RDF\GetRDFEndpointCommand;
use App\Exception\NoAccessPermission;
use App\Service\DistributionService;
use App\Service\EncryptionService;
use ARC2_StoreEndpoint;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetRDFEndpointCommandHandler implements MessageHandlerInterface
{
    private DistributionService $distributionService;

    private EncryptionService $encryptionService;

    private Security $security;

    public function __construct(DistributionService $distributionService, EncryptionService $encryptionService, Security $security)
    {
        $this->distributionService = $distributionService;
        $this->encryptionService = $encryptionService;
        $this->security = $security;
    }

    /**
     * @throws Exception
     */
    public function __invoke(GetRDFEndpointCommand $command): ARC2_StoreEndpoint
    {
        $distribution = $command->getDistribution()->getDistribution();

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        return $this->distributionService->getArc2Endpoint($distribution->getDatabaseInformation(), $this->encryptionService);
    }
}
