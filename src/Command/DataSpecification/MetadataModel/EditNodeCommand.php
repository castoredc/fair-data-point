<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\EditNodeCommand as CommonEditNodeCommand;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\XsdDataType;

class EditNodeCommand extends CommonEditNodeCommand
{
    private Node $node;

    private ?MetadataFieldType $fieldType = null;

    public function __construct(Node $node, string $title, ?string $description, string $value, ?XsdDataType $dataType, ?MetadataFieldType $fieldType)
    {
        parent::__construct($title, $description, $value, $dataType);

        $this->node = $node;
        $this->fieldType = $fieldType;
    }

    public function getNode(): Node
    {
        return $this->node;
    }

    public function getFieldType(): ?MetadataFieldType
    {
        return $this->fieldType;
    }
}
