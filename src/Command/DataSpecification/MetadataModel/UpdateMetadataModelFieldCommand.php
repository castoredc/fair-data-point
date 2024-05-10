<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\Enum\MetadataFieldType;

class UpdateMetadataModelFieldCommand
{
    private MetadataModelField $field;

    private string $title;

    private ?string $description;

    private int $order;

    private string $node;

    private MetadataFieldType $fieldType;

    private ?string $optionGroup;

    public function __construct(MetadataModelField $field, string $title, ?string $description, int $order, string $node, MetadataFieldType $fieldType, ?string $optionGroup)
    {
        $this->field = $field;
        $this->title = $title;
        $this->description = $description;
        $this->order = $order;
        $this->node = $node;
        $this->fieldType = $fieldType;
        $this->optionGroup = $optionGroup;
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
}
