<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Department;

use App\Api\Resource\Agent\AgentApiResource;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Entity\FAIRData\Agent\Department;
use function array_merge;
use function assert;

class DepartmentApiResource extends AgentApiResource
{
    public function __construct(Department $department, private bool $includeOrganization)
    {
        $this->agent = $department;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $agent = $this->agent;
        assert($agent instanceof Department);

        $data = [
            'type' => 'department',
            'additionalInformation' => $agent->getAdditionalInformation(),
        ];

        if ($this->includeOrganization) {
            $data = array_merge($data, (new OrganizationApiResource($agent->getOrganization()))->toArray());
        }

        return array_merge(parent::toArray(), $data);
    }
}
