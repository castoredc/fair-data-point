<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\Agent\AgentsApiResource;
use App\Api\Resource\ApiResource;
use App\Api\Resource\Terminology\OntologyConceptsApiResource;
use App\Entity\Metadata\StudyMetadata;
use const DATE_ATOM;

class StudyMetadataApiResource implements ApiResource
{
    public function __construct(private StudyMetadata $studyMetadata)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'studyId' => $this->studyMetadata->getStudy()->getId(),
            'id' => $this->studyMetadata->getId(),
            'briefName' => $this->studyMetadata->getBriefName(),
            'scientificName' => $this->studyMetadata->getScientificName(),
            'briefSummary' => $this->studyMetadata->getBriefSummary(),
            'summary' => $this->studyMetadata->getSummary(),
            'studyType' => $this->studyMetadata->getType()->toString(),
            'condition' => $this->studyMetadata->getCondition()?->getText(),
            'intervention' => $this->studyMetadata->getIntervention()?->getText(),
            'estimatedEnrollment' => $this->studyMetadata->getEstimatedEnrollment(),
            'estimatedStudyStartDate' => $this->studyMetadata->getEstimatedStudyStartDate()?->format('Y-m-d'),
            'estimatedStudyCompletionDate' => $this->studyMetadata->getEstimatedStudyCompletionDate()?->format('Y-m-d'),
            'recruitmentStatus' => $this->studyMetadata->getRecruitmentStatus()?->toString(),
            'methodType' => $this->studyMetadata->getMethodType()->toString(),
            'logo' => $this->studyMetadata->getLogo()?->getValue(),
            'contacts' => (new AgentsApiResource($this->studyMetadata->getContacts()))->toArray(),
            'organizations' => (new AgentsApiResource($this->studyMetadata->getOrganizations()))->toArray(),
            'version' => [
                'metadata' => $this->studyMetadata->getVersion()->getValue(),
            ],
            'issued' => $this->studyMetadata->getStudy()->getFirstMetadata()->getCreatedAt()->format(DATE_ATOM),
            'modified' => $this->studyMetadata->getCreatedAt()->format(DATE_ATOM),
            'conditions' => (new OntologyConceptsApiResource($this->studyMetadata->getConditions()->toArray()))->toArray(),
            'keywords' => $this->studyMetadata->getKeywords()?->toArray(),
        ];
    }
}
