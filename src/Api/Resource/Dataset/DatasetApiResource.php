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
    public function __construct(private Dataset $dataset)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $dataset = [
            'relativeUrl' => $this->dataset->getRelativeUrl(),
            'id' => $this->dataset->getId(),
            'slug' => $this->dataset->getSlug(),
            'defaultMetadataModel' => $this->dataset->getDefaultMetadataModel()?->getId(),
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
                'language' => $metadata->getLanguage()?->getCode(),
                'license' => $metadata->getLicense()?->getSlug(),
                'theme' => (new OntologyConceptsApiResource($metadata->getThemes()->toArray()))->toArray(),
                'keyword' => $metadata->getKeyword()?->toArray(),
                'issued' => $first->getCreatedAt()->format(DATE_ATOM),
                'modified' => $metadata->getUpdatedAt()?->format(DATE_ATOM) ?? $metadata->getCreatedAt()->format(DATE_ATOM),
            ];
        }

        return $dataset;
    }
}
