<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Command\DataSpecification\Common\Model\CreateTripleCommand as CommonCreateTripleCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Entity\Enum\NodeType;

class CreateTripleCommand extends CommonCreateTripleCommand
{
    public function __construct(
        private MetadataModelGroup $module,
        NodeType $objectType,
        NodeType $subjectType,
        ?string $objectValue,
        ?string $predicateValue,
        ?string $subjectValue,
    ) {
        parent::__construct($objectType, $subjectType, $objectValue, $predicateValue, $subjectValue);
    }

    public function getModule(): MetadataModelGroup
    {
        return $this->module;
    }
}
