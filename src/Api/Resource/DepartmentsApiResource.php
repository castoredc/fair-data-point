<?php

namespace App\Api\Resource;

use App\Entity\FAIRData\Department;

class DepartmentsApiResource implements ApiResource
{
    /** @var Department[] */
    private $departments;

    public function __construct(array $departments)
    {
        $this->departments = $departments;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->departments as $department) {
            $data[] = (new DepartmentApiResource($department))->toArray();
        }

        return $data;
    }
}