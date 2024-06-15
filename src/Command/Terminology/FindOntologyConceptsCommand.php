<?php
declare(strict_types=1);

namespace App\Command\Terminology;

class FindOntologyConceptsCommand
{
    public function __construct(private string $ontologyId, private string $query, private bool $includeIndividuals)
    {
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
