<?php

namespace App\Graph\Resource\Agent\Department;

use App\Entity\FAIRData\Department;
use App\Graph\Resource\Agent\AgentGraphResource;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use EasyRdf_Graph;

class DepartmentGraphResource extends AgentGraphResource
{
    /** @var Department */
    private $department;

    public function __construct(Department $department)
    {
        $this->department = $department;

        parent::__construct($department);
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        return (new OrganizationGraphResource($this->department->getOrganization()))->addToGraph($subject, $predicate, $graph);
    }
}