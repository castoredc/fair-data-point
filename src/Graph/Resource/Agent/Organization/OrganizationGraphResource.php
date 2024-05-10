<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent\Organization;

use App\Entity\FAIRData\Agent\Organization;
use App\Graph\Resource\Agent\AgentGraphResource;
use EasyRdf\Graph;

class OrganizationGraphResource extends AgentGraphResource
{
    public function __construct(private Organization $organization, string $baseUrl)
    {
        parent::__construct($organization, $baseUrl);
    }

    public function addToGraph(?string $subject, ?string $predicate, Graph $graph): Graph
    {
        $url = $this->baseUrl . $this->organization->getRelativeUrl();
        if ($this->organization->getHomepage() !== null) {
            $url = $this->organization->getHomepage()->getValue();
        }

        $graph->addResource($url, 'a', 'foaf:Organization');
        $graph->addLiteral($url, 'foaf:name', $this->organization->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        if ($this->organization->getCountry() !== null) {
            $graph->addResource($url, 'dcterms:spacial', $this->baseUrl . '/fdp/country/' . $this->organization->getCountry()->getCode());
        }

        return $graph;
    }
}
