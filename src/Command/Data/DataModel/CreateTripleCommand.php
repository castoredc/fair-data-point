<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\Enum\NodeType;

class CreateTripleCommand
{
    private DataModelGroup $module;

    private NodeType $objectType;

    private ?string $objectValue = null;

    private ?string $predicateValue = null;

    private NodeType $subjectType;

    private ?string $subjectValue = null;

    public function __construct(
        DataModelGroup $module,
        NodeType $objectType,
        ?string $objectValue,
        ?string $predicateValue,
        NodeType $subjectType,
        ?string $subjectValue
    ) {
        $this->module = $module;
        $this->objectType = $objectType;
        $this->objectValue = $objectValue;
        $this->predicateValue = $predicateValue;
        $this->subjectType = $subjectType;
        $this->subjectValue = $subjectValue;
    }

    public function getModule(): DataModelGroup
    {
        return $this->module;
    }

    public function getObjectType(): NodeType
    {
        return $this->objectType;
    }

    public function getObjectValue(): ?string
    {
        return $this->objectValue;
    }

    public function getPredicateValue(): ?string
    {
        return $this->predicateValue;
    }

    public function getSubjectType(): NodeType
    {
        return $this->subjectType;
    }

    public function getSubjectValue(): ?string
    {
        return $this->subjectValue;
    }
}
