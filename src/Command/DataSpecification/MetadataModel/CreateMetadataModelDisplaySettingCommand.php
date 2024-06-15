<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\MetadataDisplayPosition;
use App\Entity\Enum\MetadataDisplayType;
use App\Entity\Enum\ResourceType;

class CreateMetadataModelDisplaySettingCommand
{
    public function __construct(
        private MetadataModelVersion $metadataModelVersion,
        private string $title,
        private int $order,
        private string $node,
        private MetadataDisplayType $displayType,
        private MetadataDisplayPosition $displayPosition,
        private ResourceType $resourceType,
    ) {
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

    public function getNode(): string
    {
        return $this->node;
    }

    public function getDisplayType(): MetadataDisplayType
    {
        return $this->displayType;
    }

    public function getDisplayPosition(): MetadataDisplayPosition
    {
        return $this->displayPosition;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }
}
