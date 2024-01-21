<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateStudyMetadataCommand;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\CodedText;
use App\Entity\Version;
use App\Exception\NoAccessPermission;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStudyMetadataCommandHandler extends CreateMetadataCommandHandler
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

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

        $conditions = $this->parseOntologyConcepts($command->getConditions());

        $metadata->setConditions(new ArrayCollection($conditions));
        $metadata->setKeywords($this->parseLocalizedText($command->getKeywords()));

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
