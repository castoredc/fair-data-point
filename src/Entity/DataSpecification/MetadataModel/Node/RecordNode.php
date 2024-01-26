<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\MetadataModel\Node;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\NodeType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="metadata_model_node_record")
 */
class RecordNode extends Node
{
    public function __construct(MetadataModelVersion $metadataModelVersion)
    {
        parent::__construct($metadataModelVersion, 'Record', null);
    }

    public function getType(): ?NodeType
    {
        return NodeType::record();
    }
}
