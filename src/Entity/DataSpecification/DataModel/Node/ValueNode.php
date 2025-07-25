<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel\Node;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;
use Doctrine\ORM\Mapping as ORM;
use function assert;

#[ORM\Table(name: 'data_model_node_value')]
#[ORM\Entity]
class ValueNode extends Node
{
    #[ORM\Column(type: 'boolean')]
    private bool $isAnnotatedValue = false;

    #[ORM\Column(type: 'XsdDataType')]
    private XsdDataType $dataType;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isRepeated = false;

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

    public function getMetadataModelVersion(): MetadataModelVersion
    {
        $version = $this->getVersion();
        assert($version instanceof MetadataModelVersion);

        return $version;
    }
}
