<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelVersion;

class DataModelPrefixesApiResource implements ApiResource
{
    public function __construct(private DataModelVersion $dataModel)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataModel->getPrefixes() as $prefix) {
            $data[] = (new DataModelPrefixApiResource($prefix))->toArray();
        }

        return $data;
    }
}
