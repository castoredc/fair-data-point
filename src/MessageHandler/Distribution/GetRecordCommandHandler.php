<?php
declare(strict_types=1);

namespace App\MessageHandler\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\CastorStudyMetadataApiResource;
use App\Api\Resource\Metadata\DatabaseStudyMetadataApiResource;
use App\Api\Resource\Metadata\ManualCastorStudyMetadataApiResource;
use App\Entity\Castor\Record;
use App\Message\Distribution\GetRecordCommand;
use App\Message\Metadata\GetStudyMetadataCommand;
use App\Model\Castor\ApiClient;
use App\Security\CastorUser;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetRecordCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @return Record[]
     */
    public function __invoke(GetRecordCommand $message): array
    {
        $this->apiClient->setToken($message->getUser()->getToken());

        return [$this->apiClient->getRecord($message->getStudy(), $message->getRecordId())];
    }
}
