<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\Agent\Department\DepartmentApiResource;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\Metadata\StudyMetadata\ParticipatingCenter;

class ParticipatingCenterApiResource implements ApiResource
{
    private ParticipatingCenter $participatingCenter;

    public function __construct(ParticipatingCenter $participatingCenter)
    {
        $this->participatingCenter = $participatingCenter;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = (new OrganizationApiResource($this->participatingCenter->getOrganization()))->toArray();
        $data['departments'] = [];

        if (! $this->participatingCenter->getDepartments()->isEmpty()) {
            foreach ($this->participatingCenter->getDepartments() as $department) {
                $data['departments'][] = (new DepartmentApiResource($department, false))->toArray();
            }
        }

        return $data;
    }
}
