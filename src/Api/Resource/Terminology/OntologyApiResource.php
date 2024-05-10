<?php
declare(strict_types=1);

namespace App\Api\Resource\Terminology;

use App\Api\Resource\ApiResource;
use App\Entity\Terminology\Ontology;

class OntologyApiResource implements ApiResource
{
    public function __construct(private Ontology $ontology)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->ontology->getId(),
            'url' => $this->ontology->getUrl()->getValue(),
            'name' => $this->ontology->getName(),
        ];
    }
}
