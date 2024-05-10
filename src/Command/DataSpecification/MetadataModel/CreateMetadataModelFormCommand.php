<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\ResourceType;

class CreateMetadataModelFormCommand
{
    public function __construct(private MetadataModelVersion $metadataModelVersion, private string $title, private int $order, private ResourceType $resourceType)
    {
    }

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }
}
