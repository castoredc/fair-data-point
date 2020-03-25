<?php
declare(strict_types=1);

namespace App\MessageHandler\Api\Study;

use App\Entity\Castor\Study;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\CodedText;
use App\Message\Api\Study\CreateStudyMetadataCommand;
use App\Model\Castor\ApiClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateStudyMetadataCommandHandler implements MessageHandlerInterface
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

    public function __invoke(CreateStudyMetadataCommand $message): void
    {
        $study = $this->em->getRepository(Study::class)->find($message->getStudyId());

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
            $message->getEstimatedStudyCompletionDate()
        );

        $metadata->setStudy($study);
        $study->addMetadata($metadata);

        $this->em->persist($metadata);
        $this->em->persist($study);
        $this->em->flush();
    }
}
