<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Department;

class DepartmentApiResource implements ApiResource
{
    /** @var Department */
    private $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->department->getOrganization()->getName(),
            'country' => $this->department->getOrganization()->getCountry()->getCode(),
            'city' => $this->department->getOrganization()->getCity(),
            'department' => $this->department->getName(),
            'additionalInformation' => $this->department->getAdditionalInformation(),
        ];
    }
}
