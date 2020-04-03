<?php
declare(strict_types=1);

namespace App\Graph\Resource\Catalog;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Graph\Resource\GraphResource;
use EasyRdf_Graph;

class CatalogGraphResource implements GraphResource
{
    /** @var Catalog */
    private $catalog;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
    }

    public function toGraph(): EasyRdf_Graph
    {
        $graph = new EasyRdf_Graph();

        $graph->addResource($this->catalog->getAccessUrl(), 'a', 'dcat:Catalog');

        foreach ($this->catalog->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->catalog->getAccessUrl(), 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($this->catalog->getAccessUrl(), 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($this->catalog->getAccessUrl(), 'dcterms:hasVersion', $this->catalog->getVersion());

        foreach ($this->catalog->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->catalog->getAccessUrl(), 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        // foreach ($this->catalog->getPublishers() as $publisher) {
        //     /** @var Agent $publisher */
        //     $publisher->addToGraph($this->catalog->getAccessUrl(), 'dcterms:publisher', $graph);
        // }

        $graph->addResource($this->catalog->getAccessUrl(), 'dcterms:language', $this->catalog->getLanguage()->getAccessUrl());

        foreach ($this->catalog->getDatasets(false) as $dataset) {
            $graph->addResource($this->catalog->getAccessUrl(), 'dcat:dataset', $dataset->getAccessUrl());
        }

        $graph->addResource($this->catalog->getAccessUrl(), 'dcterms:license', $this->catalog->getLicense()->getUrl()->getValue());

        return $graph;
    }
}
