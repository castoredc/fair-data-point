<?php
declare(strict_types=1);

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

    public function toGraph(string $baseUrl = ''): EasyRdf_Graph
    {
        return $this->addToGraph($baseUrl, null, null, new EasyRdf_Graph());
    }

    public function addToGraph(string $baseUrl, ?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph
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
