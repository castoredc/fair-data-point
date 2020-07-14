<?php
declare(strict_types=1);

namespace App\Api\Request\Terminology;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;
use function boolval;

class OntologyConceptApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $ontology;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $search;

    /** @var bool|null */
    private $includeIndividuals;

    protected function parse(): void
    {
        $this->ontology = $this->getFromQuery('ontology');
        $this->search = $this->getFromQuery('query');
        $this->includeIndividuals = boolval($this->getFromQuery('includeIndividuals'));
    }

    public function getOntology(): string
    {
        return $this->ontology;
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function includeIndividuals(): bool
    {
        return $this->includeIndividuals ?? false;
    }
}
