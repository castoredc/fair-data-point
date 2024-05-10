<?php
declare(strict_types=1);

namespace App\Api\Resource\Terminology;

use App\Api\Resource\ApiResource;
use App\Entity\Terminology\Ontology;

class OntologiesApiResource implements ApiResource
{
    /** @param Ontology[] $ontologies */
    public function __construct(private array $ontologies)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->ontologies as $ontology) {
            $data[] = (new OntologyApiResource($ontology))->toArray();
        }

        return $data;
    }
}
