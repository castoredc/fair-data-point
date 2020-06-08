<?php
declare(strict_types=1);

namespace App\Message\Terminology;

class FindOntologyConceptsCommand
{
    /** @var string */
    private $ontologyId;

    /** @var string */
    private $query;

    public function __construct(string $ontologyId, string $query)
    {
        $this->ontologyId = $ontologyId;
        $this->query = $query;
    }

    public function getOntologyId(): string
    {
        return $this->ontologyId;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
