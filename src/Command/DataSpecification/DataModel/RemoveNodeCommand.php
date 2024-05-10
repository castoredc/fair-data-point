<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\Node\Node;

class RemoveNodeCommand
{
    public function __construct(private Node $node)
    {
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
