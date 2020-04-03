<?php

namespace App\Graph\Resource\Agent;

use App\Entity\FAIRData\Agent;
use App\Graph\Resource\GraphResource;
use EasyRdf_Graph;

abstract class AgentGraphResource implements GraphResource
{
    /** @var Agent */
    protected $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function toGraph(): EasyRdf_Graph
    {
        return $this->addToGraph(null, null, new EasyRdf_Graph());
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        $graph->addResource($this->agent->getAccessUrl(), 'a', 'foaf:Agent');
        $graph->addLiteral($this->agent->getAccessUrl(), 'foaf:name', $this->agent->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $this->agent->getAccessUrl());
        }

        return $graph;
    }
}