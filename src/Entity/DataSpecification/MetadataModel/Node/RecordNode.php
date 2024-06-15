<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\ResourceType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_model_node_record")
 */
class RecordNode extends Node
{
    /** @ORM\Column(type="ResourceType") */
    private ResourceType $resourceType;

    public function __construct(MetadataModelVersion $metadataModelVersion, ResourceType $resourceType)
    {
        parent::__construct($metadataModelVersion, $resourceType->getLabel(), null);

        $this->resourceType = $resourceType;
    }

    public function getType(): ?NodeType
    {
        return NodeType::record();
    }

    public function getResourceType(): ResourceType
    {
        return $this->resourceType;
    }

    public function setResourceType(ResourceType $resourceType): void
    {
        $this->resourceType = $resourceType;
    }
}
