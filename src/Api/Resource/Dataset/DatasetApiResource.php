<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\Agent\AgentsApiResource;
use App\Api\Resource\ApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Api\Resource\Terminology\OntologyConceptsApiResource;
use App\Entity\FAIRData\Dataset;
use const DATE_ATOM;

class DatasetApiResource implements ApiResource
{
    private Dataset $dataset;

    public function __construct(Dataset $dataset)
    {
        $this->dataset = $dataset;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $dataset = [
            'relativeUrl' => $this->dataset->getRelativeUrl(),
            'id' => $this->dataset->getId(),
            'slug' => $this->dataset->getSlug(),
            'hasMetadata' => $this->dataset->hasMetadata(),
            'published' => $this->dataset->isPublished(),
            'study' => $this->dataset->getStudy() !== null ? (new StudyApiResource($this->dataset->getStudy()))->toArray() : null,
            'count' => [
                'distribution' => $this->dataset->getDistributions()->count(),
            ],
        ];

        if ($this->dataset->hasMetadata()) {
            $first = $this->dataset->getFirstMetadata();
            $metadata = $this->dataset->getLatestMetadata();

            $dataset['metadata'] = [
                'title' => $metadata->getTitle()->toArray(),
                'version' => [
                    'metadata' => $metadata->getVersion()->getValue(),
                ],
                'description' => $metadata->getDescription()->toArray(),
                'publishers' => (new AgentsApiResource($metadata->getPublishers()->toArray()))->toArray(),
                'language' => $metadata->getLanguage() !== null ? $metadata->getLanguage()->getCode() : null,
                'license' => $metadata->getLicense() !== null ? $metadata->getLicense()->getSlug() : null,
                'theme' => (new OntologyConceptsApiResource($metadata->getThemes()->toArray()))->toArray(),
                'keyword' => $metadata->getKeyword() !== null ? $metadata->getKeyword()->toArray() : null,
                'issued' => $first->getCreatedAt()->format(DATE_ATOM),
                'modified' => $metadata->getUpdatedAt() !== null ? $metadata->getUpdatedAt()->format(DATE_ATOM) : $metadata->getCreatedAt()->format(DATE_ATOM),
            ];
        }

        return $dataset;
    }
}
