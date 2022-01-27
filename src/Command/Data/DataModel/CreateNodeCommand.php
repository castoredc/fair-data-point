<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;

class CreateNodeCommand
{
    private DataModelVersion $dataModelVersion;

    private NodeType $type;

    private string $title;

    private ?string $description = null;

    private string $value;

    private ?XsdDataType $dataType = null;

    private bool $isRepeated;

    public function __construct(DataModelVersion $dataModelVersion, NodeType $type, string $title, ?string $description, string $value, ?XsdDataType $dataType, ?bool $isRepeated)
    {
        $this->dataModelVersion = $dataModelVersion;
        $this->type = $type;
        $this->title = $title;
        $this->description = $description;
        $this->value = $value;
        $this->dataType = $dataType;
        $this->isRepeated = $isRepeated;
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }

    public function getType(): NodeType
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDataType(): ?XsdDataType
    {
        return $this->dataType;
    }

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }
}
