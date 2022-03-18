<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent;

use App\Api\Resource\Agent\Department\DepartmentApiResource;
use App\Api\Resource\Agent\Organization\OrganizationApiResource;
use App\Api\Resource\Agent\Person\PersonApiResource;
use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use function array_merge;

class AgentsApiResource implements ApiResource
{
    /** @var Agent[] */
    private array $agents;

    /** @param Agent[] $agents */
    public function __construct(array $agents)
    {
        $this->agents = $agents;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $organizations = [];
        $departments = [];
        $persons = [];

        foreach ($this->agents as $agent) {
            if ($agent instanceof Organization) {
                $organizations[] = [
                    'type' => 'organization',
                    'hasDepartment' => false,
                    'organization' => (new OrganizationApiResource($agent))->toArray(),
                ];
            } elseif ($agent instanceof Department) {
                $departments[] = [
                    'type' => 'organization',
                    'hasDepartment' => true,
                    'department' => (new DepartmentApiResource($agent, false))->toArray(),
                    'organization' => (new OrganizationApiResource($agent->getOrganization()))->toArray(),
                ];
            } elseif ($agent instanceof Person) {
                $persons[] = [
                    'type' => 'person',
                    'person' => (new PersonApiResource($agent))->toArray(),
                ];
            }
        }

        return array_merge($persons, $departments, $organizations);
    }
}
