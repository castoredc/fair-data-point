<?php
declare(strict_types=1);

namespace App\Exception;

use Exception;

class SparqlHttpRequestFailed extends Exception
{
    private string $response;

    public function __construct(string $response)
    {
        parent::__construct();
        $this->response = $response;
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
