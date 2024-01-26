<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\Triple;
use App\Entity\Enum\NodeType;

class UpdateTripleCommand
{
    private Triple $triple;

    private NodeType $objectType;

    private ?string $objectValue = null;

    private ?string $predicateValue = null;

    private NodeType $subjectType;

    private ?string $subjectValue = null;

    public function __construct(
        Triple $triple,
        NodeType $objectType,
        ?string $objectValue,
        ?string $predicateValue,
        NodeType $subjectType,
        ?string $subjectValue
    ) {
        $this->triple = $triple;
        $this->objectType = $objectType;
        $this->objectValue = $objectValue;
        $this->predicateValue = $predicateValue;
        $this->subjectType = $subjectType;
        $this->subjectValue = $subjectValue;
    }

    public function getTriple(): Triple
    {
        return $this->triple;
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
