<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'metadata_value')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class MetadataValue
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'metadata_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Metadata::class, inversedBy: 'values', cascade: ['persist'])]
    private Metadata $metadata;

    #[ORM\JoinColumn(name: 'value_node_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\DataSpecification\MetadataModel\Node\ValueNode::class, inversedBy: 'values', cascade: ['persist'])]
    private ValueNode $node;

    #[ORM\Column(type: 'text')]
    private string $value;

    public function __construct(Metadata $metadata, ValueNode $node, string $value)
    {
        $this->metadata = $metadata;
        $this->node = $node;
        $this->value = $value;
    }

    public function getId(): UuidInterface|string
    {
        return $this->id;
    }

    public function getMetadata(): Metadata
    {
        return $this->metadata;
    }

    public function setMetadata(Metadata $metadata): void
    {
        $this->metadata = $metadata;
    }

    public function getNode(): ValueNode
    {
        return $this->node;
    }

    public function setNode(ValueNode $node): void
    {
        $this->node = $node;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
