<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\Triple;

class TripleApiResource implements ApiResource
{
    private Triple $triple;

    public function __construct(Triple $triple)
    {
        $this->triple = $triple;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->triple->getId(),
            'subject' => (new NodeApiResource($this->triple->getSubject()))->toArray(),
            'predicate' => (new PredicateApiResource($this->triple->getPredicate()))->toArray(),
            'object' => (new NodeApiResource($this->triple->getObject()))->toArray(),
        ];
    }
}
