<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class OntologyConceptNotFound extends Exception
{
    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return ['error' => 'Ontology concept not found.'];
    }
}
