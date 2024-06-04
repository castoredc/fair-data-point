<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Enum\ResourceType;
use App\Entity\Metadata\Metadata;

interface MetadataEnrichedEntity
{
    public function hasMetadata(): bool;

    public function getFirstMetadata(): ?Metadata;

    public function getLatestMetadata(): ?Metadata;

    public function isArchived(): bool;

    public function getId(): string;

    public function getSlug(): string;

    /** @return MetadataEnrichedEntity[] */
    public function getChildren(ResourceType $resourceType): array;

    /** @return MetadataEnrichedEntity[] */
    public function getParents(ResourceType $resourceType): array;
}
