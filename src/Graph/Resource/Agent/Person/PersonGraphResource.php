<?php
declare(strict_types=1);

namespace App\Graph\Resource\Agent\Person;

use App\Entity\FAIRData\Agent\Person;
use App\Graph\Resource\Agent\AgentGraphResource;
use EasyRdf\Graph;

class PersonGraphResource extends AgentGraphResource
{
    private const ORCID_URL = 'https://orcid.org/';

    public function __construct(private Person $person, string $baseUrl)
    {
        parent::__construct($person, $baseUrl);
    }

    public function addToGraph(?string $subject, ?string $predicate, Graph $graph): Graph
    {
        $url = $this->baseUrl . $this->person->getRelativeUrl();
        if ($this->person->getOrcid() !== null) {
            $url = self::ORCID_URL . $this->person->getOrcid()->getValue();
        }

        $graph->addResource($url, 'a', 'foaf:Person');
        $graph->addLiteral($url, 'foaf:name', $this->person->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }
}
