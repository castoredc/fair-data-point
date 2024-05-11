<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\ResourceType;

class UpdateMetadataModelFieldCommand
{
    public function __construct(private MetadataModelField $field, private string $title, private ?string $description, private int $order, private string $node, private MetadataFieldType $fieldType, private ?string $optionGroup, private ResourceType $resourceType, private bool $isRequired)
    {
    }

    public function getField(): MetadataModelField
    {
        return $this->field;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function getNode(): string
    {
        return $this->node;
    }

    public function getFieldType(): MetadataFieldType
    {
        return $this->fieldType;
    }

    public function getOptionGroup(): ?string
    {
        return $this->optionGroup;
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }

    public function getIsRequired(): bool
    {
        return $this->isRequired;
    }
}
