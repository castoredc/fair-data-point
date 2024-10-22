<?php
declare(strict_types=1);

namespace App\Api\Resource\Dataset;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\MetadataApiResource;
use App\Api\Resource\Study\StudyApiResource;
use App\Entity\FAIRData\Dataset;

class DatasetApiResource implements ApiResource
{
    public function __construct(private Dataset $dataset)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'relativeUrl' => $this->dataset->getRelativeUrl(),
            'id' => $this->dataset->getId(),
            'slug' => $this->dataset->getSlug(),
            'defaultMetadataModel' => $this->dataset->getDefaultMetadataModel()?->getId(),
            'hasMetadata' => $this->dataset->hasMetadata(),
            'metadata' => $this->dataset->hasMetadata() ? (new MetadataApiResource($this->dataset->getLatestMetadata()))->toArray() : null,
            'published' => $this->dataset->isPublished(),
            'study' => $this->dataset->getStudy() !== null ? (new StudyApiResource($this->dataset->getStudy()))->toArray() : null,
        ];
    }
}
