<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use App\Entity\FAIRData\Country;
use EasyRdf\Graph;

class CountryGraphResource extends GraphResource
{
    public function __construct(private Country $country, string $baseUrl)
    {
        parent::__construct($country, $baseUrl);
    }

    public function toGraph(): Graph
    {
        $graph = new Graph();

        $graph->addResource($this->getUrl(), 'a', 'dcterms:Location');
        $graph->addLiteral($this->getUrl(), 'dcterms:title', $this->country->getName(), 'en');

        return $graph;
    }
}
