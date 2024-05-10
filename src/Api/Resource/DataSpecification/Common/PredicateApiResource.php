<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Model\Predicate;

class PredicateApiResource implements ApiResource
{
    public function __construct(private Predicate $predicate)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->predicate->getId(),
            'value' => (new IriApiResource($this->predicate->getDataSpecificationVersion(), $this->predicate->getIri()))->toArray(),
            'base' => $this->predicate->getIri()->getBase(),
        ];
    }
}
