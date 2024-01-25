<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use function assert;

class DataModelModulesApiResource implements ApiResource
{
    private DataModelVersion $dataModel;

    private bool $groupTriples;

    public function __construct(DataModelVersion $dataModel, bool $groupTriples = true)
    {
        $this->dataModel = $dataModel;
        $this->groupTriples = $groupTriples;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataModel->getGroups() as $group) {
            assert($group instanceof DataModelGroup);
            $data[] = (new DataModelModuleApiResource($group, $this->groupTriples))->toArray();
        }

        return $data;
    }
}
