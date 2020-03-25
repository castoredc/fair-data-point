<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\Metadata\StudyMetadata;

class DatabaseStudyMetadataApiResource implements ApiResource
{
    /** @var StudyMetadata */
    private $studyMetadata;

    public function __construct(StudyMetadata $studyMetadata)
    {
        $this->studyMetadata = $studyMetadata;
    }

    public function toArray(): array
    {
        return [
            'studyId' => $this->studyMetadata->getStudy()->getId(),
            'source' => 'database',
            'metadata' => [
                'id' => $this->studyMetadata->getId(),
                'briefName' => $this->studyMetadata->getBriefName(),
                'scientificName' => $this->studyMetadata->getScientificName(),
                'briefSummary' => $this->studyMetadata->getBriefSummary(),
                'summary' => $this->studyMetadata->getSummary(),
                'studyType' => $this->studyMetadata->getType()->toString(),
                'condition' => $this->studyMetadata->getCondition()->getText(),
                'intervention' => $this->studyMetadata->getIntervention()->getText(),
                'estimatedEnrollment' => $this->studyMetadata->getEstimatedEnrollment(),
                'estimatedStudyStartDate' => $this->studyMetadata->getEstimatedStudyStartDate()->format('Y-m-d'),
                'estimatedStudyCompletionDate' => $this->studyMetadata->getEstimatedStudyCompletionDate()->format('Y-m-d'),
            ],
        ];
    }
}
