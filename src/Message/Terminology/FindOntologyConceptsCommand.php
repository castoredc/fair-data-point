<?php
declare(strict_types=1);

namespace App\Message\Terminology;

class FindOntologyConceptsCommand
{
    private string $ontologyId;

    private string $query;

    private bool $includeIndividuals;

    public function __construct(string $ontologyId, string $query, bool $includeIndividuals)
    {
        $this->ontologyId = $ontologyId;
        $this->query = $query;
        $this->includeIndividuals = $includeIndividuals;
    }

    public function getOntologyId(): string
    {
        return $this->ontologyId;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function includeIndividuals(): bool
    {
        return $this->includeIndividuals;
    }
}
