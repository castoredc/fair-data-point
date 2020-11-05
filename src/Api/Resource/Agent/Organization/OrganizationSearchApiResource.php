<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Organization;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\Grid\Institute;

class OrganizationSearchApiResource implements ApiResource
{
    /** @var array<Organization|Institute> */
    private array $organizations;

    /** @param array<Organization|Institute> $organizations */
    public function __construct(array $organizations)
    {
        $this->organizations = $organizations;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->organizations as $organization) {
            if ($organization instanceof Organization) {
                $data[] = [
                    'value' => $organization->getId(),
                    'label' => $organization->getName(),
                    'data' => (new OrganizationApiResource($organization))->toArray(),
                    'source' => 'database',
                ];
            } else {
                $data[] = [
                    'value' => $organization->getId(),
                    'label' => $organization->getName(),
                    'data' => (new GridInstituteApiResource($organization))->toArray(),
                    'source' => 'grid',
                ];
            }
        }

        return $data;
    }
}
