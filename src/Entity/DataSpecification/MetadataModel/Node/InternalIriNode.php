<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\Enum\NodeType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_model_node_internal')]
#[ORM\Entity]
class InternalIriNode extends Node
{
    #[ORM\Column(type: 'string')]
    private string $slug;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isRepeated = false;

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getType(): ?NodeType
    {
        return NodeType::internalIri();
    }

    public function getValue(): ?string
    {
        return $this->slug;
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
