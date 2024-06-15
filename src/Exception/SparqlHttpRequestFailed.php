<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class SparqlHttpRequestFailed extends Exception
{
    public function __construct(private ?string $response = null)
    {
        parent::__construct();
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'error' => 'An error occurred while getting data using a SPARQL query.',
            'details' => $this->response,
        ];
    }
}
