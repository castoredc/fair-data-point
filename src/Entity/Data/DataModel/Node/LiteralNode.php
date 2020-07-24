<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Node;

use App\Entity\Enum\NodeType;
use App\Entity\Enum\RecordDetailLiterals;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_model_node_literal")
 */
class LiteralNode extends Node
{
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $value;

    /**
     * @ORM\Column(type="XsdDataType")
     *
     * @var XsdDataType
     */
    private $dataType;

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getType(): ?NodeType
    {
        return NodeType::literal();
    }

    public function getDataType(): XsdDataType
    {
        return $this->dataType;
    }

    public function setDataType(XsdDataType $dataType): void
    {
        $this->dataType = $dataType;
    }

    public function isPlaceholder(): bool
    {
        return RecordDetailLiterals::canBeConstructedFromString($this->value);
    }

    public function getPlaceholderType(): RecordDetailLiterals
    {
        return RecordDetailLiterals::fromString($this->value);
    }
}
