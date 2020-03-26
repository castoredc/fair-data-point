<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Metadata\StudyMetadata;
use App\Message\Api\Study\UpdateStudyMetadataCommand;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateStudyMetadataCommandHandler implements MessageHandlerInterface
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

    public function __invoke(UpdateStudyMetadataCommand $message): void
    {
        $message->getMetadata()->setBriefName($message->getBriefName());
        $message->getMetadata()->setScientificName($message->getScientificName());
        $message->getMetadata()->setBriefSummary($message->getBriefSummary());
        $message->getMetadata()->setSummary($message->getSummary());
        $message->getMetadata()->setType($message->getType());
        $message->getMetadata()->getCondition()->setText($message->getCondition());
        $message->getMetadata()->getIntervention()->setText($message->getIntervention());
        $message->getMetadata()->setEstimatedEnrollment($message->getEstimatedEnrollment());
        $message->getMetadata()->setEstimatedStudyStartDate($message->getEstimatedStudyStartDate());
        $message->getMetadata()->setEstimatedStudyCompletionDate($message->getEstimatedStudyCompletionDate());

        $this->em->persist($message->getMetadata());

        $this->em->flush();
    }
}
