<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\Predicate;

class PredicateApiResource implements ApiResource
{
    private Predicate $predicate;

    public function __construct(Predicate $predicate)
    {
        $this->predicate = $predicate;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->predicate->getId(),
            'value' => (new IriApiResource($this->predicate->getDataModel(), $this->predicate->getIri()))->toArray(),
            'base' => $this->predicate->getIri()->getBase(),
        ];
    }
}
