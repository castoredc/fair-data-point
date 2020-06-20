<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Record;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\GetRecordCommand;
use App\Model\Castor\ApiClient;
use App\Security\CastorUser;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class GetRecordCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    /** @var Security */
    private $security;

    public function __construct(ApiClient $apiClient, Security $security)
    {
        $this->apiClient = $apiClient;
        $this->security = $security;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(GetRecordCommand $command): Record
    {
        $distribution = $command->getDistribution();
        $study = $distribution->getDataset()->getStudy();
        assert($study instanceof CastorStudy);

        $user = $this->security->getUser();
        assert($user instanceof CastorUser);

        if (! $this->security->isGranted('access_data', $distribution)) {
            throw new NoAccessPermission();
        }

        $apiUser = $distribution->getApiUser();

        if ($apiUser !== null) {
            $this->apiClient->useApiUser($apiUser);
        } else {
            $this->apiClient->setUser($user);
        }

        return $this->apiClient->getRecord($study, $command->getRecordId());
    }
}
