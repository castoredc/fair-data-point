<?php

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\CodedText;
use App\Message\Api\Study\CreateStudyMetadataCommand;
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

    public function __invoke(UpdateStudyMetadataCommand $message)
    {
        /** @var StudyMetadata $metadata */
        $metadata = $this->em->getRepository(StudyMetadata::class)->find($message->getMetadataId());

        $metadata->setBriefName($message->getBriefName());
        $metadata->setScientificName($message->getScientificName());
        $metadata->setBriefSummary($message->getBriefSummary());
        $metadata->setSummary($message->getSummary());
        $metadata->setType($message->getType());
        $metadata->getCondition()->setText($message->getCondition());
        $metadata->getIntervention()->setText($message->getIntervention());
        $metadata->setEstimatedEnrollment($message->getEstimatedEnrollment());
        $metadata->setEstimatedStudyStartDate($message->getEstimatedStudyStartDate());
        $metadata->setEstimatedStudyCompletionDate($message->getEstimatedStudyCompletionDate());

        $this->em->persist($metadata);

        $this->em->flush();
    }
}