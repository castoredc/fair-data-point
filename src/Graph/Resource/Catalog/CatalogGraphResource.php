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

    public function toGraph(string $baseUrl): EasyRdf_Graph
    {
        $graph = new EasyRdf_Graph();
        $url = $baseUrl . $this->catalog->getRelativeUrl();
        $metadata = $this->catalog->getLatestMetadata();

        $graph->addResource($url, 'a', 'dcat:Catalog');

        foreach ($metadata->getTitle()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($url, 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($url, 'dcterms:hasVersion', $metadata->getVersion());

        foreach ($metadata->getDescription()->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($url, 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addResource($url, 'dcterms:language', $metadata->getLanguage()->getAccessUrl());

        foreach ($this->catalog->getDatasets(false) as $dataset) {
            $graph->addResource($url, 'dcat:dataset', $baseUrl . $dataset->getRelativeUrl());
        }

        $graph->addResource($url, 'dcterms:license', $metadata->getLicense()->getUrl()->getValue());

        return $graph;
    }
}
