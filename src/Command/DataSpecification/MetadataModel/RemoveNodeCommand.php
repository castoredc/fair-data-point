<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\Node\Node;

class RemoveNodeCommand
{
    private Node $node;

    public function __construct(Node $node)
    {
        $this->node = $node;
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
