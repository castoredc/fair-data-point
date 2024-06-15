<?php
declare(strict_types=1);

namespace App\Api\Resource\Terminology;

use App\Api\Resource\ApiResource;
use App\Entity\Terminology\OntologyConcept;

class OntologyConceptApiResource implements ApiResource
{
    public function __construct(private OntologyConcept $concept)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'code' => $this->concept->getCode(),
            'url' => $this->concept->getUrl()->getValue(),
            'displayName' => $this->concept->getDisplayName(),
            'ontology' => (new OntologyApiResource($this->concept->getOntology()))->toArray(),
        ];
    }
}
