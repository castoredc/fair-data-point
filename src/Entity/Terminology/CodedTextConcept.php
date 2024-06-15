<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

class CodedTextConcept
{
    public function __construct(private OntologyConcept $ontologyConcept, private int $positionFrom, private int $positionTo)
    {
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
