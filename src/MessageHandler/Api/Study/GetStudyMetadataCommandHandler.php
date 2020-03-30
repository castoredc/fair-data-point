<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\ApiResource;
use App\Api\Resource\CastorStudyMetadataApiResource;
use App\Api\Resource\DatabaseStudyMetadataApiResource;
use App\Api\Resource\ManualCastorStudyMetadataApiResource;
use App\Message\Api\Study\GetStudyMetadataCommand;
use App\Model\Castor\ApiClient;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyMetadataCommandHandler implements MessageHandlerInterface
{
    /** @var ApiClient */
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function __invoke(GetStudyMetadataCommand $message): ApiResource
    {
        if ($message->getStudy()->hasMetadata()) {
            return new DatabaseStudyMetadataApiResource($message->getStudy()->getLatestMetadata());
        }

        if ($message->getStudy()->isEnteredManually()) {
            return new ManualCastorStudyMetadataApiResource($message->getStudy());
        }

        $this->apiClient->setToken($message->getUser()->getToken());
        $study = $this->apiClient->getStudy($message->getStudy()->getId());

        return new CastorStudyMetadataApiResource($study);
    }
}
