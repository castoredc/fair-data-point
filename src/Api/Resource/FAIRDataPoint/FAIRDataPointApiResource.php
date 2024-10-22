<?php
declare(strict_types=1);

namespace App\Api\Resource\FAIRDataPoint;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Metadata\MetadataApiResource;
use App\Entity\FAIRData\FAIRDataPoint;

class FAIRDataPointApiResource implements ApiResource
{
    public function __construct(private FAIRDataPoint $fairDataPoint)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'relativeUrl' => $this->fairDataPoint->getRelativeUrl(),
            'iri' => $this->fairDataPoint->getIri(),
            'hasMetadata' => $this->fairDataPoint->hasMetadata(),
            'defaultMetadataModel' => $this->fairDataPoint->getDefaultMetadataModel()?->getId(),
            'metadata' => $this->fairDataPoint->hasMetadata() ? (new MetadataApiResource($this->fairDataPoint->getLatestMetadata()))->toArray() : null,
        ];
    }
}
