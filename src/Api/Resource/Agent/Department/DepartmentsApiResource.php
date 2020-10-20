<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Department;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Department;

class DepartmentsApiResource implements ApiResource
{
    /** @var Department[] */
    private array $departments;

    /**
     * @param Department[] $departments
     */
    public function __construct(array $departments)
    {
        $this->departments = $departments;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->departments as $department) {
            $data[] = (new DepartmentApiResource($department))->toArray();
        }

        return $data;
    }
}
