<?php
declare(strict_types=1);

namespace App\Api\Request\Terminology;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

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

    protected function parse(): void
    {
        $this->ontology = $this->getFromQuery('ontology');
        $this->search = $this->getFromQuery('query');
    }

    public function getOntology(): string
    {
        return $this->ontology;
    }

    public function getSearch(): string
    {
        return $this->search;
    }
}
