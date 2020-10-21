<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent\Department;

use App\Entity\FAIRData\Agent\Department;
use App\Graph\Resource\Agent\AgentGraphResource;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use EasyRdf\Graph;

class DepartmentGraphResource extends AgentGraphResource
{
    private Department $department;

    public function __construct(Department $department)
    {
        $this->department = $department;

        parent::__construct($department);
    }

    public function addToGraph(string $baseUrl, ?string $subject, ?string $predicate, Graph $graph): Graph
    {
        return (new OrganizationGraphResource($this->department->getOrganization()))->addToGraph($baseUrl, $subject, $predicate, $graph);
    }
}
