<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_node_value")
 */
class ValueNode extends Node
{
    /** @ORM\Column(type="boolean") */
    private bool $isAnnotatedValue = false;

    /** @ORM\Column(type="XsdDataType") */
    private XsdDataType $dataType;

    /** @ORM\OneToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\MetadataModelField", mappedBy="node") */
    private ?MetadataModelField $field = null;

    public function isAnnotatedValue(): bool
    {
        return $this->isAnnotatedValue;
    }

    public function setIsAnnotatedValue(bool $isAnnotatedValue): void
    {
        $this->isAnnotatedValue = $isAnnotatedValue;
    }

    public function getType(): ?NodeType
    {
        return NodeType::value();
    }

    public function getValue(): ?string
    {
        return $this->isAnnotatedValue ? 'annotated' : 'plain';
    }

    public function getDataType(): ?XsdDataType
    {
        return $this->isAnnotatedValue ? null : $this->dataType;
    }

    public function setDataType(XsdDataType $dataType): void
    {
        $this->dataType = $dataType;
    }

    public function getField(): ?MetadataModelField
    {
        return $this->field;
    }

    public function hasField(): bool
    {
        return $this->field !== null;
    }

    public function usedAsTitle(): ?ResourceType
    {
        if ($this->getMetadataModelVersion()->getTitleNode(ResourceType::catalog()) === $this) {
            return ResourceType::catalog();
        }

        if ($this->getMetadataModelVersion()->getTitleNode(ResourceType::dataset()) === $this) {
            return ResourceType::dataset();
        }

        if ($this->getMetadataModelVersion()->getTitleNode(ResourceType::distribution()) === $this) {
            return ResourceType::distribution();
        }

        if ($this->getMetadataModelVersion()->getTitleNode(ResourceType::fdp()) === $this) {
            return ResourceType::fdp();
        }

        return null;
    }
}
