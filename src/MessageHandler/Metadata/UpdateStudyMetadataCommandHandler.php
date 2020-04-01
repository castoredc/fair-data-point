<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Terminology\CodedText;
use App\Message\Metadata\UpdateStudyMetadataCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateStudyMetadataCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UpdateStudyMetadataCommand $message): void
    {
        $condition = $message->getMetadata()->getCondition();
        $intervention = $message->getMetadata()->getIntervention();

        if ($condition === null && $message->getCondition() !== null) {
            $condition = new CodedText($message->getCondition());
        } elseif ($condition !== null && $message->getCondition() !== null) {
            $condition->setText($message->getCondition());
        } else {
            $condition = null;
        }

        if ($intervention === null && $message->getIntervention() !== null) {
            $intervention = new CodedText($message->getIntervention());
        } elseif ($intervention !== null && $message->getIntervention() !== null) {
            $intervention->setText($message->getIntervention());
        } else {
            $intervention = null;
        }

        $message->getMetadata()->setBriefName($message->getBriefName());
        $message->getMetadata()->setScientificName($message->getScientificName());
        $message->getMetadata()->setBriefSummary($message->getBriefSummary());
        $message->getMetadata()->setSummary($message->getSummary());
        $message->getMetadata()->setType($message->getType());
        $message->getMetadata()->setCondition($condition);
        $message->getMetadata()->setIntervention($intervention);
        $message->getMetadata()->setEstimatedEnrollment($message->getEstimatedEnrollment());
        $message->getMetadata()->setEstimatedStudyStartDate($message->getEstimatedStudyStartDate());
        $message->getMetadata()->setEstimatedStudyCompletionDate($message->getEstimatedStudyCompletionDate());
        $message->getMetadata()->setRecruitmentStatus($message->getRecruitmentStatus());
        $message->getMetadata()->setMethodType($message->getMethodType());

        $this->em->persist($message->getMetadata());

        $this->em->flush();
    }
}
