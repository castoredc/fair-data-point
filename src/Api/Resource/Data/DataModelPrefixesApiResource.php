<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelVersion;

class DataModelPrefixesApiResource implements ApiResource
{
    /** @var DataModelVersion */
    private $dataModel;

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

        foreach ($this->dataModel->getPrefixes() as $prefix) {
            $data[] = (new DataModelPrefixApiResource($prefix))->toArray();
        }

        return $data;
    }
}
