<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Metadata\Metadata;
use const DATE_ATOM;

class MetadataApiResource implements ApiResource
{
    public function __construct(private Metadata $metadata)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->metadata->getId(),
            'version' => $this->metadata->getVersion()->getValue(),
            'model' => $this->metadata->getMetadataModelVersion()?->getMetadataModel()?->getId(),
            'modelVersion' => $this->metadata->getMetadataModelVersion()?->getId(),
            'createdAt' => $this->metadata->getCreatedAt()->format(DATE_ATOM),
            'modifiedAt' => $this->metadata->getUpdatedAt()?->format(DATE_ATOM) ?? $this->metadata->getCreatedAt()->format(DATE_ATOM),
            'title' => $this->metadata->getTitle()?->toArray(),
        ];
    }
}
