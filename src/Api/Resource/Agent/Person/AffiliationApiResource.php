<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Person;

use App\Api\Resource\Agent\Department\DepartmentApiResource;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Affiliation;

class AffiliationApiResource implements ApiResource
{
    public function __construct(private Affiliation $affiliation)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'organization' => (new OrganizationApiResource($this->affiliation->getOrganization()))->toArray(),
            'department' => (new DepartmentApiResource($this->affiliation->getDepartment(), false))->toArray(),
            'position' => $this->affiliation->getPosition(),
        ];
    }
}
