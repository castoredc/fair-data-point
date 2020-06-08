<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Entity\Data\DataModel\DataModel;

class DataModelModulesApiResource implements ApiResource
{
    /** @var DataModel */
    private $dataModel;

    public function __construct(DataModel $dataModel)
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
