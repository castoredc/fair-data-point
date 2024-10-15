<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\DataModel\Node;

use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\Enum\NodeType;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'data_model_node_record')]
#[ORM\Entity]
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
