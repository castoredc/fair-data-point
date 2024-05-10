<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataModel;

use App\Entity\DataSpecification\DataModel\Triple;

class DeleteTripleCommand
{
    public function __construct(private Triple $triple)
    {
    }

    public function getTriple(): Triple
    {
        return $this->triple;
    }
}
