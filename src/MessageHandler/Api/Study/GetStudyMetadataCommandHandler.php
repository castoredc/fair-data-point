<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Api\Resource\ApiResource;
use App\Api\Resource\CastorStudyMetadataApiResource;
use App\Api\Resource\DatabaseStudyMetadataApiResource;
use App\Entity\Castor\Study;
use App\Message\Api\Study\GetStudyMetadataCommand;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetStudyMetadataCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var ApiClient */
    private $apiClient;

    public function __construct(EntityManagerInterface $em, ApiClient $apiClient)
    {
        $this->em = $em;
        $this->apiClient = $apiClient;
    }

    public function __invoke(GetStudyMetadataCommand $message): ApiResource
    {
        /** @var Study|null $study */
        $study = $this->em->getRepository(Study::class)->find($message->getStudyId());

        if ($study->hasMetadata()) {
            $metadata = $study->getMetadata()->last();

            return new DatabaseStudyMetadataApiResource($metadata);
        }

        $this->apiClient->setToken($message->getUser()->getToken());
        $study = $this->apiClient->getStudy($message->getStudyId());

        return new CastorStudyMetadataApiResource($study);
    }
}
