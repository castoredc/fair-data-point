<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\XsdDataType;

class EditNodeCommand
{
    private Node $node;

    private string $title;

    private ?string $description = null;

    private string $value;

    private ?XsdDataType $dataType = null;

    private bool $isRepeated;

    public function __construct(Node $node, string $title, ?string $description, string $value, ?XsdDataType $dataType, ?bool $isRepeated)
    {
        $this->node = $node;
        $this->title = $title;
        $this->description = $description;
        $this->value = $value;
        $this->dataType = $dataType;
        $this->isRepeated = $isRepeated;
    }

    public function getNode(): Node
    {
        return $this->node;
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
