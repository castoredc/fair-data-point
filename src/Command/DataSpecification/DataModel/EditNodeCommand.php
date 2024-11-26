<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\EditNodeCommand as CommonEditNodeCommand;
use App\Entity\DataSpecification\DataModel\Node\Node;
use App\Entity\Enum\XsdDataType;

class EditNodeCommand extends CommonEditNodeCommand
{
    public function __construct(
        private Node $node,
        string $title,
        string $value,
        ?string $description,
        ?XsdDataType $dataType,
        ?bool $isRepeated,
    ) {
        parent::__construct($title, $value, $description, $dataType, $isRepeated);
    }

    public function getNode(): Node
    {
        return $this->node;
    }
}
