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
        $url = $this->catalog->getAccessUrl();

        $graph->addResource($url, 'a', 'dcat:Catalog');

        foreach ($this->catalog->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($url, 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($url, 'dcterms:hasVersion', $this->catalog->getVersion());

        foreach ($this->catalog->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addResource($url, 'dcterms:language', $this->catalog->getLanguage()->getAccessUrl());

        foreach ($this->catalog->getDatasets(false) as $dataset) {
            $graph->addResource($url, 'dcat:dataset', $dataset->getAccessUrl());
        }

        $graph->addResource($url, 'dcterms:license', $this->catalog->getLicense()->getUrl()->getValue());

        return $graph;
    }
}
