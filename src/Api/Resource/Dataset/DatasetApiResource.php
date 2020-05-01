<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\Agent\Department\DepartmentApiResource;
use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\FAIRData\Person;
use Doctrine\Common\Collections\ArrayCollection;

class DatasetApiResource implements ApiResource
{
    /** @var Dataset */
    private $dataset;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $study = $this->dataset->getStudy();
        $metadata = $study->getLatestMetadata();

        if ($metadata === null) {
            return [
                'id' => $this->dataset->getId(),
                'studyId' => $study->getId(),
                'slug' => $this->dataset->getSlug(),
                'hasMetadata' => false
            ];
        }

        $contactPoints = [];
        foreach ($metadata->getContacts() as $contactPoint) {
            if (! $contactPoint instanceof Person) {
                continue;
            }

            $contactPoints[] = (new PersonApiResource($contactPoint))->toArray();
        }

        $organizations = [];
        foreach ($metadata->getDepartments() as $department) {
            /** @var Department $department */
            $organizations[] = (new DepartmentApiResource($department))->toArray();
        }

        $title = new LocalizedText(new ArrayCollection([new LocalizedTextItem($metadata->getBriefName(), $this->dataset->getLanguage())]));

        $shortDescription = null;

        if ($metadata->getBriefSummary() !== null) {
            $shortDescription = (new LocalizedText(new ArrayCollection([new LocalizedTextItem($metadata->getBriefSummary(), $this->dataset->getLanguage())])))->toArray();
        }

        $description = null;

        if ($metadata->getSummary() !== null) {
            $description = (new LocalizedText(new ArrayCollection([new LocalizedTextItem($metadata->getSummary(), $this->dataset->getLanguage())])))->toArray();
        }

        return [
            'accessUrl' => $this->dataset->getAccessUrl(),
            'relativeUrl' => $this->dataset->getRelativeUrl(),
            'id' => $this->dataset->getId(),
            'studyId' => $study->getId(),
            'slug' => $this->dataset->getSlug(),
            'title' => $title->toArray(),
            'version' => $study->getLatestMetadataVersion(),
            'shortDescription' => $shortDescription,
            'description' => $description,
            'publishers' => [],
            'language' => $this->dataset->getLanguage()->toArray(),
            'license' => $this->dataset->getLicense() !== null ? $this->dataset->getLicense()->toArray() : null,
            'issued' => $metadata->getCreated(),
            'modified' => $metadata->getUpdated(),
            'contactPoints' => $contactPoints,
            'organizations' => $organizations,
            'landingPage' => $this->dataset->getLandingPage() !== null ? $this->dataset->getLandingPage()->getValue() : null,
            'logo' => $metadata->getLogo() !== null ? $metadata->getLogo()->getValue() : null,
            'recruitmentStatus' => $metadata->getRecruitmentStatus() !== null ? $metadata->getRecruitmentStatus()->toString() : null,
            'estimatedEnrollment' => $metadata->getEstimatedEnrollment(),
            'studyType' => $metadata->getType()->toString(),
            'methodType' => $metadata->getMethodType() !== null ? $metadata->getMethodType()->toString() : null,
            'condition' => $metadata->getCondition() !== null ? $metadata->getCondition()->toArray() : null,
            'intervention' => $metadata->getIntervention() !== null ? $metadata->getIntervention()->toArray() : null,
            'published' => $this->dataset->isPublished(),
            'hasMetadata' => true
        ];
    }
}
