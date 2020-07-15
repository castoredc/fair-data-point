<?php
declare(strict_types=1);

namespace App\Entity\Data\DataModel\Node;

use App\Entity\Data\DataModel\DataModelVersion;
use App\Entity\Enum\NodeType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_model_node_record")
 */
class RecordNode extends Node
{
    public function __construct(DataModelVersion $dataModel)
    {
        parent::__construct($dataModel, 'Record', null);
    }

    public function getType(): ?NodeType
    {
        return NodeType::record();
    }
}
