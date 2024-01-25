<?php
declare(strict_types=1);

namespace App\Command\Data\DataModel;

use App\Entity\DataSpecification\DataModel\Triple;

class DeleteTripleCommand
{
    private Triple $triple;

    public function __construct(Triple $triple)
    {
        $this->triple = $triple;
    }

    public function getTriple(): Triple
    {
        return $this->triple;
    }
}
