<?php
declare(strict_types=1);

namespace App\Api\Resource\Catalog;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\MetadataApiResource;
use App\Entity\FAIRData\Catalog;

class CatalogApiResource implements ApiResource
{
    public function __construct(private Catalog $catalog)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'relativeUrl' => $this->catalog->getRelativeUrl(),
            'id' => $this->catalog->getId(),
            'slug' => $this->catalog->getSlug(),
            'defaultMetadataModel' => $this->catalog->getDefaultMetadataModel()?->getId(),
            'acceptSubmissions' => $this->catalog->isAcceptingSubmissions(),
            'submissionAccessesData' => $this->catalog->isSubmissionAccessingData(),
            'hasMetadata' => $this->catalog->hasMetadata(),
            'metadata' => $this->catalog->hasMetadata() ? (new MetadataApiResource($this->catalog->getLatestMetadata()))->toArray() : null,
        ];
    }
}
