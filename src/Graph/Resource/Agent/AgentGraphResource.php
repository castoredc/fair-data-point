<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent;

use App\Entity\FAIRData\Agent\Agent;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

abstract class AgentGraphResource implements GraphResource
{
    protected Agent $agent;

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }

    public function toGraph(string $baseUrl = ''): Graph
    {
        return $this->addToGraph($baseUrl, null, null, new Graph());
    }

    public function addToGraph(string $baseUrl, ?string $subject, ?string $predicate, Graph $graph): Graph
    {
        $url = $baseUrl . $this->agent->getRelativeUrl();
        $graph->addResource($url, 'a', 'foaf:Agent');
        $graph->addLiteral($url, 'foaf:name', $this->agent->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }
}
