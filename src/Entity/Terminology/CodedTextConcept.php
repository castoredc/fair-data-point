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
}
