<?php
declare(strict_types=1);

namespace App\Graph\Resource\FAIRDataPoint;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

class FAIRDataPointGraphResource implements GraphResource
{
    private FAIRDataPoint $fairDataPoint;

    public function __construct(FAIRDataPoint $fairDataPoint)
    {
        $this->fairDataPoint = $fairDataPoint;
    }

    public function toGraph(string $baseUrl): Graph
    {
        $graph = new Graph();

        $url = $baseUrl . $this->fairDataPoint->getRelativeUrl();

        $graph->addResource($url, 'a', 'r3d:Repository');

        foreach ($this->fairDataPoint->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($url, 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($url, 'dcterms:hasVersion', $this->fairDataPoint->getVersion());

        foreach ($this->fairDataPoint->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        // foreach ($this->fairDataPoint->getPublishers() as $publisher) {
        //     /** @var Agent $publisher */
        //     $publisher->addToGraph($this->fairDataPoint->getAccessUrl(), 'dcterms:publisher', $graph);
        // }

        $graph->addResource($url, 'dcterms:language', $this->fairDataPoint->getLanguage()->getAccessUrl());

        $graph->addResource($url, 'dcterms:license', $this->fairDataPoint->getLicense()->getUrl()->getValue());

        foreach ($this->fairDataPoint->getCatalogs() as $catalog) {
            /** @var Catalog $catalog */
            $graph->addResource($url, 'http://www.re3data.org/schema/3-0#dataCatalog', $baseUrl . $catalog->getRelativeUrl());
        }

        return $graph;
    }
}
