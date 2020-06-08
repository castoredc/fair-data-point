<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Node;

use App\Entity\Enum\NodeType;
use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_model_node_external")
 */
class ExternalIriNode extends Node
{
    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $iri;

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
