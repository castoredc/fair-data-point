<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\Triple;
use App\Entity\Enum\NodeType;

class UpdateTripleCommand
{
    /** @var Triple */
    private $triple;

    /** @var NodeType */
    private $objectType;

    /** @var string|null */
    private $objectValue;

    /** @var string|null */
    private $predicateValue;

    /** @var NodeType */
    private $subjectType;

    /** @var string|null */
    private $subjectValue;

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
