<?php
declare(strict_types=1);

namespace App\Message\Data;

use App\Entity\Data\DataModel\Triple;

class DeleteTripleCommand
{
    /** @var Triple */
    private $triple;

    public function __construct(Triple $triple)
    {
        $this->triple = $triple;
    }

    public function getTriple(): Triple
    {
        return $this->triple;
    }
}
