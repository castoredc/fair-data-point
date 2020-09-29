<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Node\Node;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class DataModelNodeMapping extends DataModelMapping
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\Node\Node")
     * @ORM\JoinColumn(name="node", referencedColumnName="id")
     */
    private ?Node $node = null;

    public function __construct(RDFDistribution $distribution, Node $node, CastorEntity $entity, DataModelVersion $dataModelVersion)
    {
        parent::__construct($distribution, $entity, $dataModelVersion);

        $this->node = $node;
    }

    public function getNode(): ?Node
    {
        return $this->node;
    }

    public function setNode(Node $node): void
    {
        $this->node = $node;
    }
}
