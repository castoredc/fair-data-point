<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelVersion;

class PredicatesApiResource implements ApiResource
{
    private DataModelVersion $dataModel;

    public function __construct(DataModelVersion $dataModel)
    {
        $this->dataModel = $dataModel;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataModel->getPredicates() as $predicate) {
            $data[] = (new PredicateApiResource($predicate))->toArray();
        }

        return $data;
    }
}
