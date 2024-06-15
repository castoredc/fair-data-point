<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\MetadataModel\Triple;

class TripleApiResource implements ApiResource
{
    public function __construct(private Triple $triple)
    {
    }

    /** @return array<mixed> */
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
