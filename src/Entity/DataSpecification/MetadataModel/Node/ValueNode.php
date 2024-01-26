<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="metadata_model_node_value")
 */
class ValueNode extends Node
{
    /** @ORM\Column(type="boolean") */
    private bool $isAnnotatedValue = false;

    /** @ORM\Column(type="XsdDataType") */
    private XsdDataType $dataType;

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
}
