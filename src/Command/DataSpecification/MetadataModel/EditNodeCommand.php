<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\EditNodeCommand as CommonEditNodeCommand;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\XsdDataType;

class EditNodeCommand extends CommonEditNodeCommand
{
    private Node $node;

    public function __construct(Node $node, string $title, ?string $description, string $value, ?XsdDataType $dataType)
    {
        parent::__construct($title, $description, $value, $dataType);

        $this->node = $node;
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
