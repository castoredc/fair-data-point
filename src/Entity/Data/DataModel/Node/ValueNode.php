<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Node;

use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_model_node_value")
 */
class ValueNode extends Node
{
    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isAnnotatedValue = false;

    /**
     * @ORM\Column(type="XsdDataType")
     *
     * @var XsdDataType
     */
    private $dataType;

    /**
     * @ORM\Column(type="boolean", options={"default":"0"})
     *
     * @var bool
     */
    private $isRepeated;

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

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }

    public function setIsRepeated(bool $isRepeated): void
    {
        $this->isRepeated = $isRepeated;
    }
}
