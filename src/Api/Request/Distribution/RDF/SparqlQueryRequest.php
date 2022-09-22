<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution\RDF;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class SparqlQueryRequest extends SingleApiRequest
{
    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $sparqlQuery;

    protected function parse(): void
    {
        $this->sparqlQuery = $this->getFromData('query');
    }

    public function getSparqlQuery(): string
    {
        return $this->sparqlQuery;
    }
}
