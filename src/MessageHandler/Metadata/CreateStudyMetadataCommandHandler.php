<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\CodedText;
use App\Exception\NoAccessPermission;
use App\Message\Metadata\CreateStudyMetadataCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateStudyMetadataCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateStudyMetadataCommand $message): void
    {
        $study = $message->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $metadata = new StudyMetadata($study);

        $metadata->setBriefName($message->getBriefName());
        $metadata->setScientificName($message->getScientificName());
        $metadata->setBriefSummary($message->getBriefSummary());
        $metadata->setSummary($message->getSummary());
        $metadata->setType($message->getType());

        if ($message->getCondition() !== null) {
            $metadata->setCondition(new CodedText($message->getCondition()));
        }

        if ($message->getIntervention() !== null) {
            $metadata->setCondition(new CodedText($message->getIntervention()));
        }

        $metadata->setEstimatedEnrollment($message->getEstimatedEnrollment());
        $metadata->setEstimatedStudyStartDate($message->getEstimatedStudyStartDate());
        $metadata->setEstimatedStudyCompletionDate($message->getEstimatedStudyCompletionDate());
        $metadata->setRecruitmentStatus($message->getRecruitmentStatus());
        $metadata->setMethodType($message->getMethodType());

        $study->addMetadata($metadata);

        $this->em->persist($metadata);
        $this->em->persist($study);
        $this->em->flush();
    }
}
