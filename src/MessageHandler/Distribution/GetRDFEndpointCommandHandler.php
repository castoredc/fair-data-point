<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Connection\DistributionService;
use App\Encryption\EncryptionService;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\GetRDFEndpointCommand;
use ARC2_StoreEndpoint;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetRDFEndpointCommandHandler implements MessageHandlerInterface
{
    /** @var DistributionService */
    private $distributionService;

    /** @var EncryptionService */
    private $encryptionService;

    /** @var Security */
    private $security;

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
