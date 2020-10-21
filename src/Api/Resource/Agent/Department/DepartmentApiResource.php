<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Department;

use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Department;
use function array_merge;

class DepartmentApiResource implements ApiResource
{
    private Department $department;
    private bool $includeOrganization;

    public function __construct(Department $department, bool $includeOrganization)
    {
        $this->department = $department;
        $this->includeOrganization = $includeOrganization;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [
            'type' => 'department',
            'id' => $this->department->getId(),
            'name' => $this->department->getName(),
            'additionalInformation' => $this->department->getAdditionalInformation(),
        ];

        if ($this->includeOrganization) {
            $data = array_merge($data, (new OrganizationApiResource($this->department->getOrganization()))->toArray());
        }

        return $data;
    }
}
