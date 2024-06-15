<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Organization;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Organization;

class OrganizationsApiResource implements ApiResource
{
    /** @param Organization[] $organizations */
    public function __construct(private array $organizations)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->organizations as $organization) {
            $data[] = (new OrganizationApiResource($organization))->toArray();
        }

        return $data;
    }
}
