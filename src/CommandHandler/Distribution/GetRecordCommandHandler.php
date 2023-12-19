<?php
declare(strict_types=1);

namespace App\CommandHandler\Distribution;

use App\Command\Distribution\GetRecordCommand;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Security\User;
use App\Service\EncryptionService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class GetRecordCommandHandler
{
    private ApiClient $apiClient;
    private Security $security;
    private EncryptionService $encryptionService;

    public function __construct(ApiClient $apiClient, Security $security, EncryptionService $encryptionService)
    {
        $this->apiClient = $apiClient;
        $this->security = $security;
        $this->encryptionService = $encryptionService;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     * @throws UserNotACastorUser
     */
    public function __invoke(GetRecordCommand $command): Record
    {
        $distribution = $command->getDistribution();
        $study = $distribution->getDataset()->getStudy();
        assert($study instanceof CastorStudy);

        $user = $this->security->getUser();
        assert($user instanceof User);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $apiUser = $distribution->getApiUser();

        if ($apiUser !== null) {
            $this->apiClient->useApiUser($apiUser, $this->encryptionService);
        } else {
            if (! $user->hasCastorUser()) {
                throw new UserNotACastorUser();
            }

            $this->apiClient->setUser($user->getCastorUser());
        }

        return $this->apiClient->getRecord($study, $command->getRecordId());
    }
}
