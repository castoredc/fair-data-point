<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\EditNodeCommand as CommonEditNodeCommand;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\XsdDataType;

class EditNodeCommand extends CommonEditNodeCommand
{
    public function __construct(private Node $node, string $title, ?string $description, string $value, ?XsdDataType $dataType, private ?ResourceType $useAsTitle)
    {
        parent::__construct($title, $description, $value, $dataType);
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function getUseAsTitle(): ?ResourceType
    {
        return $this->useAsTitle;
    }
}
