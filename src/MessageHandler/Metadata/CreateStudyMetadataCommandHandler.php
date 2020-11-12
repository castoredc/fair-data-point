<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\CodedText;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use App\Message\Metadata\CreateStudyMetadataCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateStudyMetadataCommandHandler implements MessageHandlerInterface
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateStudyMetadataCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $metadata = new StudyMetadata($study);

        $metadata->setVersion(new Version(self::DEFAULT_VERSION_NUMBER));

        $metadata->setBriefName($command->getBriefName());
        $metadata->setScientificName($command->getScientificName());
        $metadata->setBriefSummary($command->getBriefSummary());
        $metadata->setSummary($command->getSummary());
        $metadata->setType($command->getType());

        if ($command->getCondition() !== null) {
            $metadata->setCondition(new CodedText($command->getCondition()));
        }

        if ($command->getIntervention() !== null) {
            $metadata->setCondition(new CodedText($command->getIntervention()));
        }

        $metadata->setEstimatedEnrollment($command->getEstimatedEnrollment());
        $metadata->setEstimatedStudyStartDate($command->getEstimatedStudyStartDate());
        $metadata->setEstimatedStudyCompletionDate($command->getEstimatedStudyCompletionDate());
        $metadata->setRecruitmentStatus($command->getRecruitmentStatus());
        $metadata->setMethodType($command->getMethodType());

        $study->addMetadata($metadata);

        $this->em->persist($metadata);
        $this->em->persist($study);
        $this->em->flush();
    }
}
