<?php
declare(strict_types=1);

namespace App\Api\Resource\Terminology;

use App\Api\Resource\ApiResource;
use App\Entity\Terminology\OntologyConcept;

class OntologyConceptsApiResource implements ApiResource
{
    /** @var OntologyConcept[] */
    private array $concepts;

    /**
     * @param OntologyConcept[] $concepts
     */
    public function __construct(array $concepts)
    {
        $this->concepts = $concepts;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->concepts as $concept) {
            $data[] = (new OntologyConceptApiResource($concept))->toArray();
        }

        return $data;
    }
}
