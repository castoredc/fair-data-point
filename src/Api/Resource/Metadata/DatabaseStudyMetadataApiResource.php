<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Metadata\StudyMetadata;

class DatabaseStudyMetadataApiResource implements ApiResource
{
    /** @var StudyMetadata */
    private $studyMetadata;

    public function __construct(StudyMetadata $studyMetadata)
    {
        $this->studyMetadata = $studyMetadata;
    }

    /**
     * @return array<mixed>
     */
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
                'condition' => $this->studyMetadata->getCondition() !== null ? $this->studyMetadata->getCondition()->getText() : null,
                'intervention' => $this->studyMetadata->getIntervention() !== null ? $this->studyMetadata->getIntervention()->getText() : null,
                'estimatedEnrollment' => $this->studyMetadata->getEstimatedEnrollment(),
                'estimatedStudyStartDate' => $this->studyMetadata->getEstimatedStudyStartDate() !== null ? $this->studyMetadata->getEstimatedStudyStartDate()->format('Y-m-d') : null,
                'estimatedStudyCompletionDate' => $this->studyMetadata->getEstimatedStudyCompletionDate() !== null ? $this->studyMetadata->getEstimatedStudyCompletionDate()->format('Y-m-d') : null,
                'recruitmentStatus' => $this->studyMetadata->getRecruitmentStatus() !== null ? $this->studyMetadata->getRecruitmentStatus()->toString() : null,
                'methodType' => $this->studyMetadata->getMethodType() !== null ? $this->studyMetadata->getMethodType()->toString() : null,
            ],
        ];
    }
}
