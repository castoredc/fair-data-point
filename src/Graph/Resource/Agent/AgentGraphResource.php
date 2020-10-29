<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent;

use App\Entity\FAIRData\Agent\Agent;
use EasyRdf\Graph;

abstract class AgentGraphResource
{
    protected Agent $agent;
    protected string $baseUrl;

    public function __construct(Agent $agent, string $baseUrl)
    {
        $this->agent = $agent;
        $this->baseUrl = $baseUrl;
    }

    public function toGraph(): Graph
    {
        return $this->addToGraph(null, null, new Graph());
    }

    public function addToGraph(?string $subject, ?string $predicate, Graph $graph): Graph
    {
        $url = $this->baseUrl . $this->agent->getRelativeUrl();
        $graph->addResource($url, 'a', 'foaf:Agent');
        $graph->addLiteral($url, 'foaf:name', $this->agent->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }
}
