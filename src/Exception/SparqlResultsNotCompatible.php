<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class SparqlResultsNotCompatible extends Exception
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        return ['error' => 'The results of the federated query across multiple distributions are not compatible'];
    }
}
