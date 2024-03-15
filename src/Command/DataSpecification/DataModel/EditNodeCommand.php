<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\EditNodeCommand as CommonEditNodeCommand;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\XsdDataType;

class EditNodeCommand extends CommonEditNodeCommand
{
    private Node $node;

    private bool $isRepeated;

    public function __construct(Node $node, string $title, ?string $description, string $value, ?XsdDataType $dataType, ?bool $isRepeated)
    {
        parent::__construct($title, $description, $value, $dataType);

        $this->node = $node;
        $this->isRepeated = $isRepeated;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function isRepeated(): bool
    {
        return $this->isRepeated;
    }
}
