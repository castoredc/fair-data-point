<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

use App\Entity\Enum\NodeType;

abstract class UpdateTripleCommand
{
    public function __construct(
        private NodeType $objectType,
        private ?string $objectValue = null,
        private ?string $predicateValue = null,
        private NodeType $subjectType,
        private ?string $subjectValue = null,
    ) {
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
