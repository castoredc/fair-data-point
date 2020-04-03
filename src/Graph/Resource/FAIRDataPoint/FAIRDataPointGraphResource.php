<?php
declare(strict_types=1);

namespace App\Graph\Resource\FAIRDataPoint;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Graph\Resource\GraphResource;
use EasyRdf_Graph;

class FAIRDataPointGraphResource implements GraphResource
{
    /** @var FAIRDataPoint */
    private $fairDataPoint;

    public function __construct(FAIRDataPoint $fairDataPoint)
    {
        $this->fairDataPoint = $fairDataPoint;
    }

    public function toGraph(): EasyRdf_Graph
    {
        $graph = new EasyRdf_Graph();

        $graph->addResource($this->fairDataPoint->getAccessUrl(), 'a', 'r3d:Repository');

        foreach ($this->fairDataPoint->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->fairDataPoint->getAccessUrl(), 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($this->fairDataPoint->getAccessUrl(), 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($this->fairDataPoint->getAccessUrl(), 'dcterms:hasVersion', $this->fairDataPoint->getVersion());

        foreach ($this->fairDataPoint->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->fairDataPoint->getAccessUrl(), 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        // foreach ($this->fairDataPoint->getPublishers() as $publisher) {
        //     /** @var Agent $publisher */
        //     $publisher->addToGraph($this->fairDataPoint->getAccessUrl(), 'dcterms:publisher', $graph);
        // }

        $graph->addResource($this->fairDataPoint->getAccessUrl(), 'dcterms:language', $this->fairDataPoint->getLanguage()->getAccessUrl());

        $graph->addResource($this->fairDataPoint->getAccessUrl(), 'dcterms:license', $this->fairDataPoint->getLicense()->getUrl()->getValue());

        foreach ($this->fairDataPoint->getCatalogs() as $catalog) {
            /** @var Catalog $catalog */
            $graph->addResource($this->fairDataPoint->getAccessUrl(), 'http://www.re3data.org/schema/3-0#dataCatalog', $catalog->getAccessUrl());
        }

        return $graph;
    }
}
