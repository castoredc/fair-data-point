<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

class CodedTextConcept
{
    /** @var OntologyConcept */
    private $ontologyConcept;

    /** @var int */
    private $positionFrom;

    /** @var int */
    private $positionTo;

    public function __construct(OntologyConcept $ontologyConcept, int $positionFrom, int $positionTo)
    {
        $this->ontologyConcept = $ontologyConcept;
        $this->positionFrom = $positionFrom;
        $this->positionTo = $positionTo;
    }

    public function getOntologyConcept(): OntologyConcept
    {
        return $this->ontologyConcept;
    }

    public function setOntologyConcept(OntologyConcept $ontologyConcept): void
    {
        $this->ontologyConcept = $ontologyConcept;
    }

    public function getPositionFrom(): int
    {
        return $this->positionFrom;
    }

    public function setPositionFrom(int $positionFrom): void
    {
        $this->positionFrom = $positionFrom;
    }

    public function getPositionTo(): int
    {
        return $this->positionTo;
    }

    public function setPositionTo(int $positionTo): void
    {
        $this->positionTo = $positionTo;
    }
}
