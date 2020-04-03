<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\CastorStudyMetadataApiResource;
use App\Api\Resource\Metadata\DatabaseStudyMetadataApiResource;
use App\Api\Resource\Metadata\ManualCastorStudyMetadataApiResource;
use App\Message\Metadata\GetStudyMetadataCommand;
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

        $this->apiClient->setUser($message->getUser());
        $study = $this->apiClient->getStudy($message->getStudy()->getId());

        return new CastorStudyMetadataApiResource($study);
    }
}
