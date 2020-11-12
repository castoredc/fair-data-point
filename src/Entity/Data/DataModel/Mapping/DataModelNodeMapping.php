<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Mapping;

use App\Entity\Castor\CastorEntity;
use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Data\DataModel\Node\Node;
use App\Entity\Study;
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

    public function __construct(Study $study, Node $node, CastorEntity $entity, DataModelVersion $dataModelVersion)
    {
        parent::__construct($study, $entity, $dataModelVersion);

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
