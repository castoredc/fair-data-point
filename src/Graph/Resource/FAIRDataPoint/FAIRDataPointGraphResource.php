<?php
declare(strict_types=1);

namespace App\Graph\Resource\FAIRDataPoint;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

class FAIRDataPointGraphResource extends GraphResource
{
    private FAIRDataPoint $fairDataPoint;

    public function __construct(FAIRDataPoint $fairDataPoint, string $baseUrl)
    {
        $this->fairDataPoint = $fairDataPoint;
        parent::__construct($fairDataPoint, $baseUrl);
    }

    public function toGraph(): Graph
    {
        $graph = new Graph();

        $graph->addResource($this->getUrl(), 'a', 'r3d:Repository');

        foreach ($this->fairDataPoint->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->getUrl(), 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($this->getUrl(), 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($this->getUrl(), 'dcterms:hasVersion', $this->fairDataPoint->getVersion());

        foreach ($this->fairDataPoint->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->getUrl(), 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addResource($this->getUrl(), 'dcterms:language', $this->fairDataPoint->getLanguage()->getAccessUrl());

        $graph->addResource($this->getUrl(), 'dcterms:license', $this->fairDataPoint->getLicense()->getUrl()->getValue());

        foreach ($this->fairDataPoint->getCatalogs() as $catalog) {
            /** @var Catalog $catalog */
            $graph->addResource($this->getUrl(), 'http://www.re3data.org/schema/3-0#dataCatalog', $this->baseUrl . $catalog->getRelativeUrl());
        }

        return $graph;
    }
}
