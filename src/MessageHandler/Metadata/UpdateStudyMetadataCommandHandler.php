<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Terminology\CodedText;
use App\Exception\NoAccessPermission;
use App\Message\Metadata\UpdateStudyMetadataCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateStudyMetadataCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(UpdateStudyMetadataCommand $command): void
    {
        $study = $command->getMetadata()->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $condition = $command->getMetadata()->getCondition();
        $intervention = $command->getMetadata()->getIntervention();

        if ($condition === null && $command->getCondition() !== null) {
            $condition = new CodedText($command->getCondition());
        } elseif ($condition !== null && $command->getCondition() !== null) {
            $condition->setText($command->getCondition());
        } else {
            $condition = null;
        }

        if ($intervention === null && $command->getIntervention() !== null) {
            $intervention = new CodedText($command->getIntervention());
        } elseif ($intervention !== null && $command->getIntervention() !== null) {
            $intervention->setText($command->getIntervention());
        } else {
            $intervention = null;
        }

        $command->getMetadata()->setBriefName($command->getBriefName());
        $command->getMetadata()->setScientificName($command->getScientificName());
        $command->getMetadata()->setBriefSummary($command->getBriefSummary());
        $command->getMetadata()->setSummary($command->getSummary());
        $command->getMetadata()->setType($command->getType());
        $command->getMetadata()->setCondition($condition);
        $command->getMetadata()->setIntervention($intervention);
        $command->getMetadata()->setEstimatedEnrollment($command->getEstimatedEnrollment());
        $command->getMetadata()->setEstimatedStudyStartDate($command->getEstimatedStudyStartDate());
        $command->getMetadata()->setEstimatedStudyCompletionDate($command->getEstimatedStudyCompletionDate());
        $command->getMetadata()->setRecruitmentStatus($command->getRecruitmentStatus());
        $command->getMetadata()->setMethodType($command->getMethodType());

        $this->em->persist($command->getMetadata());

        $this->em->flush();
    }
}
