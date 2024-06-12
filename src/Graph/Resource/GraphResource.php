<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Graph\Resource\Agent\Department\DepartmentGraphResource;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use App\Graph\Resource\Agent\Person\PersonGraphResource;
use EasyRdf\Graph;

abstract class GraphResource
{
    protected string $url;

    public function __construct(AccessibleEntity $entity, protected string $baseUrl)
    {
        $this->url = $entity->getRelativeUrl();
    }

    public function toGraph(): Graph
    {
        return new Graph();
    }

    protected function getIdentifierURL(): string
    {
        return $this->getUrl() . '#identifier';
    }

    /** @param Agent[] $agents */
    protected function addAgentsToGraph(string $predicate, array $agents, Graph $graph): Graph
    {
        foreach ($agents as $agent) {
            if ($agent instanceof Department) {
                $graph = (new DepartmentGraphResource($agent, $this->baseUrl))->addToGraph($this->getUrl(), $predicate, $graph);
            }

            if ($agent instanceof Organization) {
                $graph = (new OrganizationGraphResource($agent, $this->baseUrl))->addToGraph($this->getUrl(), $predicate, $graph);
            }

            if (! ($agent instanceof Person)) {
                continue;
            }

            $graph = (new PersonGraphResource($agent, $this->baseUrl))->addToGraph($this->getUrl(), $predicate, $graph);
        }

        return $graph;
    }

    protected function getUrl(): string
    {
        return $this->baseUrl . $this->url;
    }
}
