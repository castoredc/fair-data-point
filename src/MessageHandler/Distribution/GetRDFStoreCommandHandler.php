<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Connection\DistributionService;
use App\Encryption\EncryptionService;
use App\Exception\NoAccessPermission;
use App\Message\Distribution\GetRDFStoreCommand;
use ARC2_Store;
use Exception;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class GetRDFStoreCommandHandler implements MessageHandlerInterface
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
    public function __invoke(GetRDFStoreCommand $command): ARC2_Store
    {
        $distribution = $command->getDistribution()->getDistribution();

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        return $this->distributionService->getArc2Store(DistributionService::CURRENT_STORE, $distribution->getDatabaseInformation(), $this->encryptionService);
    }
}
