<?php
declare(strict_types=1);

namespace App\Graph\Resource;

use App\Entity\FAIRData\Country;
use EasyRdf\Graph;

class CountryGraphResource extends GraphResource
{
    private Country $country;

    public function __construct(Country $country, string $baseUrl)
    {
        $this->country = $country;
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
