<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModel;

class DataModelsApiResource implements ApiResource
{
    /** @var DataModel[] */
    private array $dataModels;

    /** @param DataModel[] $dataModels */
    public function __construct(array $dataModels)
    {
        $this->dataModels = $dataModels;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataModels as $dataModel) {
            $data[] = (new DataModelApiResource($dataModel))->toArray();
        }

        return $data;
    }
}
