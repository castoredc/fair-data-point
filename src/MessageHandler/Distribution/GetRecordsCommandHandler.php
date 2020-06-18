<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Entity\Castor\Record;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Message\Distribution\GetRecordsCommand;
use App\Model\Castor\ApiClient;
use App\Security\CastorUser;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;
use function assert;

class GetRecordsCommandHandler implements MessageHandlerInterface
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
     * @return Record[]
     *
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function __invoke(GetRecordsCommand $command): array
    {
        $user = $this->security->getUser();
        assert($user instanceof CastorUser);

        // if ($message->getDistribution()->getContents()->getAccessRights() === DistributionAccessType::PUBLIC) {
        //     $this->apiClient->useApiUser($message->getCatalog()->getApiUser());
        // } else {
        //     $this->apiClient->setUser($message->getUser());
        // }

        $this->apiClient->setUser($user);

        return $this->apiClient->getRecords($command->getStudy())->toArray();
    }
}
