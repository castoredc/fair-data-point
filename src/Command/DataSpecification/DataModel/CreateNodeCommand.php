<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\CreateNodeCommand as CommonCreateNodeCommand;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use App\Entity\Enum\NodeType;
use App\Entity\Enum\XsdDataType;

class CreateNodeCommand extends CommonCreateNodeCommand
{
    public function __construct(
        private DataModelVersion $dataModelVersion,
        NodeType $type,
        string $title,
        string $value,
        ?string $description,
        ?XsdDataType $dataType,
        ?bool $isRepeated,
    ) {
        parent::__construct($type, $title, $value, $description, $dataType, $isRepeated);
    }

    public function getDataModelVersion(): DataModelVersion
    {
        return $this->dataModelVersion;
    }
}
