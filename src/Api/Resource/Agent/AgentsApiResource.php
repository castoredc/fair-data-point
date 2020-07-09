<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent;

use App\Api\Resource\Agent\Department\DepartmentApiResource;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Entity\FAIRData\Person;

class AgentsApiResource implements ApiResource
{
    /** @var Agent[] */
    private $agents;

    /**
     * @param Agent[] $agents
     */
    public function __construct(array $agents)
    {
        $this->agents = $agents;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->agents as $agent) {
            if ($agent instanceof Organization) {
                $data[] = (new OrganizationApiResource($agent))->toArray();
            } elseif ($agent instanceof Department) {
                $data[] = (new DepartmentApiResource($agent))->toArray();
            } elseif ($agent instanceof Person) {
                $data[] = (new PersonApiResource($agent))->toArray();
            }
        }

        return $data;
    }
}
