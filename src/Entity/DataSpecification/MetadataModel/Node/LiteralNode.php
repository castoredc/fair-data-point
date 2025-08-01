<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\Enum\MetadataPlaceholderLiterals;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_model_node_literal')]
#[ORM\Entity]
class LiteralNode extends Node
{
    #[ORM\Column(type: 'string')]
    private string $value;

    #[ORM\Column(type: 'XsdDataType')]
    private XsdDataType $dataType;

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
        return MetadataPlaceholderLiterals::canBeConstructedFromString($this->value);
    }

    public function getPlaceholderType(): MetadataPlaceholderLiterals
    {
        return MetadataPlaceholderLiterals::fromString($this->value);
    }
}
