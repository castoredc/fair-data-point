<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent\Organization;

use App\Entity\FAIRData\Organization;
use App\Graph\Resource\Agent\AgentGraphResource;
use EasyRdf\Graph;

class OrganizationGraphResource extends AgentGraphResource
{
    private Organization $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;

        parent::__construct($organization);
    }

    public function addToGraph(string $baseUrl, ?string $subject, ?string $predicate, Graph $graph): Graph
    {
        $url = $baseUrl . $this->organization->getRelativeUrl();
        if ($this->organization->getHomepage() !== null) {
            $url = $this->organization->getHomepage()->getValue();
        }

        $graph->addResource($url, 'a', 'foaf:Organization');
        $graph->addLiteral($url, 'foaf:name', $this->organization->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }
}
