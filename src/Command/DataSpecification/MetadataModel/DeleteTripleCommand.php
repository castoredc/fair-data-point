<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\Triple;

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
