<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent\Person;

use App\Entity\FAIRData\Person;
use App\Graph\Resource\Agent\AgentGraphResource;
use EasyRdf\Graph;

class PersonGraphResource extends AgentGraphResource
{
    private Person $person;

    public function __construct(Person $person)
    {
        $this->person = $person;

        parent::__construct($person);
    }

    public function addToGraph(string $baseUrl, ?string $subject, ?string $predicate, Graph $graph): Graph
    {
        $url = $baseUrl . $this->person->getRelativeUrl();
        if ($this->person->getOrcid() !== null) {
            $url = $this->person->getOrcid()->getValue();
        }

        $graph->addResource($url, 'a', 'foaf:Person');
        $graph->addLiteral($url, 'foaf:name', $this->person->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }
}
