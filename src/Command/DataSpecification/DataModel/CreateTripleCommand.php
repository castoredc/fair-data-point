<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Command\DataSpecification\Common\Model\CreateTripleCommand as CommonCreateTripleCommand;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\Enum\NodeType;

class CreateTripleCommand extends CommonCreateTripleCommand
{
    public function __construct(
        private DataModelGroup $module,
        NodeType $objectType,
        NodeType $subjectType,
        ?string $objectValue,
        ?string $predicateValue,
        ?string $subjectValue,
    ) {
        parent::__construct($objectType, $subjectType, $objectValue, $predicateValue, $subjectValue);
    }

    public function getModule(): DataModelGroup
    {
        return $this->module;
    }
}
