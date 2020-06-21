<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataModel\Node\Node;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_rdf_mappings")
 * @ORM\HasLifecycleCallbacks
 */
class DataModelMapping
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="RDFDistribution", inversedBy="mappings",cascade={"persist"})
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id", nullable=false)
     *
     * @var RDFDistribution
     */
    private $distribution;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Data\DataModel\Node\Node")
     * @ORM\JoinColumn(name="node", referencedColumnName="id", nullable=false)
     *
     * @var Node
     */
    private $node;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\CastorEntity")
     * @ORM\JoinColumn(name="entity", referencedColumnName="id", nullable=false)
     *
     * @var CastorEntity
     */
    private $entity;

    public function __construct(RDFDistribution $distribution, Node $node, CastorEntity $entity)
    {
        $this->distribution = $distribution;
        $this->node = $node;
        $this->entity = $entity;
    }

    public function getDistribution(): RDFDistribution
    {
        return $this->distribution;
    }

    public function setDistribution(RDFDistribution $distribution): void
    {
        $this->distribution = $distribution;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function setNode(Node $node): void
    {
        $this->node = $node;
    }

    public function getEntity(): CastorEntity
    {
        return $this->entity;
    }

    public function setEntity(CastorEntity $entity): void
    {
        $this->entity = $entity;
    }
}
