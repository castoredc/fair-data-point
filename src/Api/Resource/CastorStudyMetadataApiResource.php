<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\Castor\Study;

class CastorStudyMetadataApiResource implements ApiResource
{
    /** @var Study */
    private $study;

    public function __construct(Study $study)
    {
        $this->study = $study;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'studyId' => $this->study->getId(),
            'source' => 'castor',
            'metadata' => [
                'briefName' => $this->study->getName(),
                'scientificName' => '',
                'briefSummary' => '',
                'summary' => '',
                'studyType' => '',
                'condition' => '',
                'intervention' => '',
                'estimatedEnrollment' => '',
                'estimatedStudyStartDate' => '',
                'estimatedStudyCompletionDate' => '',
                'methodType' => '',
            ],
        ];
    }
}
