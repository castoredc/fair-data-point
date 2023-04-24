<?php
declare(strict_types=1);

namespace App\Graph;

use EasyRdf\Graph;
use EasyRdf\Sparql\Result;

class SparqlResponse
{
    private string $response;
    private string $contentType;
    private string $queryUri;
    private ?Result $result = null;
    private ?Graph $graph = null;

    public function __construct(string $response, string $contentType, string $queryUri)
    {
        $this->response = $response;
        $this->contentType = $contentType;
        $this->queryUri = $queryUri;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function setResult(?Result $result): void
    {
        $this->result = $result;
    }

    public function setGraph(?Graph $graph): void
    {
        $this->graph = $graph;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function getGraph(): ?Graph
    {
        return $this->graph;
    }

    public function getQueryUri(): string
    {
        return $this->queryUri;
    }

    public function setQueryUri(string $queryUri): void
    {
        $this->queryUri = $queryUri;
    }

    public function getResultCount(): int
    {
        return $this->result !== null ? $this->result->numRows() : 0;
    }
}
