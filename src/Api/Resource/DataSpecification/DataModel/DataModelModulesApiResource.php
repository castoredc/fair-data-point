<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\DataModelVersion;
use function assert;

class DataModelModulesApiResource implements ApiResource
{
    public function __construct(private DataModelVersion $dataModel, private bool $groupTriples = true)
    {
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
