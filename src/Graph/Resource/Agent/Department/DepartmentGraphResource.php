<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent\Department;

use App\Entity\FAIRData\Agent\Department;
use App\Graph\Resource\Agent\AgentGraphResource;
use App\Graph\Resource\Agent\Organization\OrganizationGraphResource;
use EasyRdf\Graph;

class DepartmentGraphResource extends AgentGraphResource
{
    public function __construct(private Department $department, string $baseUrl)
    {
        parent::__construct($department, $baseUrl);
    }

    public function addToGraph(?string $subject, ?string $predicate, Graph $graph): Graph
    {
        return (new OrganizationGraphResource($this->department->getOrganization(), $this->baseUrl))->addToGraph($subject, $predicate, $graph);
    }
}
