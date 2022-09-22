<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution\RDF;

use App\Api\Request\ApiRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class SparqlQueryRequest extends ApiRequest
{
    public function __construct(Request $request)
    {
        $data = $request->request->all();
        $this->query = $request->query;

        parent::__construct($data, $request->query);
    }

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private string $sparqlQuery;

    protected function parse(): void
    {
        $this->sparqlQuery = $this->getFromData('query') ?? $this->getFromQuery('query');
    }

    public function getSparqlQuery(): string
    {
        return $this->sparqlQuery;
    }
}
