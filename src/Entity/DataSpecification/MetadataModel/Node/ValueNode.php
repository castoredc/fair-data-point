<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\DataSpecification\Common\OptionGroup;
use App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;
use function assert;

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

    /** @ORM\Column(type="MetadataFieldType", nullable=true) */
    private ?MetadataFieldType $fieldType = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\MetadataModelOptionGroup")
     * @ORM\JoinColumn(name="option_group_id", referencedColumnName="id")
     */
    private OptionGroup|MetadataModelOptionGroup|null $optionGroup = null;

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

    public function getFieldType(): ?MetadataFieldType
    {
        return $this->fieldType;
    }

    public function setFieldType(?MetadataFieldType $fieldType): void
    {
        $this->fieldType = $fieldType;
    }

    public function getOptionGroup(): ?MetadataModelOptionGroup
    {
        if ($this->optionGroup instanceof MetadataModelOptionGroup) {
            return $this->optionGroup;
        }

        return null;
    }

    public function setOptionGroup(?OptionGroup $optionGroup): void
    {
        assert($optionGroup instanceof MetadataModelOptionGroup);

        $this->optionGroup = $optionGroup;
    }
}
