<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModelVersion;

class DataModelModulesApiResource implements ApiResource
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

        foreach ($this->dataModel->getModules() as $module) {
            $data[] = (new DataModelModuleApiResource($module))->toArray();
        }

        return $data;
    }
}
