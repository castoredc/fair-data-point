<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\XsdDataType;
use App\Entity\Metadata\MetadataValue;
use Doctrine\Common\Collections\Collection;
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

    /** @ORM\OneToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting", mappedBy="node") */
    private ?MetadataModelDisplaySetting $displaySetting = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\MetadataValue", mappedBy="node")
     *
     * @var Collection<MetadataValue>
     */
    private Collection $values;

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

    public function hasDisplaySetting(): bool
    {
        return $this->displaySetting !== null;
    }

    public function hasValues(): bool
    {
        return $this->values->count() > 0;
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

    public function getDisplaySetting(): ?MetadataModelDisplaySetting
    {
        return $this->displaySetting;
    }

    /** @return Collection<MetadataValue> */
    public function getValues(): Collection
    {
        return $this->values;
    }

    /** @param Collection<MetadataValue> $values */
    public function setValues(Collection $values): void
    {
        $this->values = $values;
    }
}
