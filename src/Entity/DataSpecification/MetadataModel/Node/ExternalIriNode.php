<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\Enum\NodeType;
use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'metadata_model_node_external')]
#[ORM\Entity]
class ExternalIriNode extends Node
{
    #[ORM\Column(type: 'iri', nullable: true)]
    private ?Iri $iri = null;

    public function getIri(): ?Iri
    {
        return $this->iri;
    }

    public function setIri(?Iri $iri): void
    {
        $this->iri = $iri;
    }

    public function getType(): ?NodeType
    {
        return NodeType::externalIri();
    }

    public function getValue(): ?string
    {
        return $this->iri->getValue();
    }
}
