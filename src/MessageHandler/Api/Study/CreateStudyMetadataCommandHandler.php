<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\CodedText;
use App\Message\Api\Study\CreateStudyMetadataCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateStudyMetadataCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateStudyMetadataCommand $message): void
    {
        $metadata = new StudyMetadata(
            $message->getBriefName(),
            $message->getScientificName(),
            $message->getBriefSummary(),
            $message->getSummary(),
            $message->getType(),
            new CodedText($message->getCondition()),
            new CodedText($message->getIntervention()),
            $message->getEstimatedEnrollment(),
            $message->getEstimatedStudyStartDate(),
            $message->getEstimatedStudyCompletionDate(),
            $message->getRecruitmentStatus(),
            $message->getMethodType()
        );

        $metadata->setStudy($message->getStudy());
        $message->getStudy()->addMetadata($metadata);

        $this->em->persist($metadata);
        $this->em->persist($message->getStudy());
        $this->em->flush();
    }
}
