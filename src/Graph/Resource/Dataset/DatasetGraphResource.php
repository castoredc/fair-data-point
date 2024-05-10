<?php
declare(strict_types=1);

namespace App\Graph\Resource\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

class DatasetGraphResource extends GraphResource
{
    public function __construct(private Dataset $dataset, string $baseUrl)
    {
        parent::__construct($dataset, $baseUrl);
    }

    public function toGraph(): Graph
    {
        $graph = new Graph();
        $metadata = $this->dataset->getLatestMetadata();

        if ($metadata === null) {
            return $graph;
        }

        $graph->addResource($this->getUrl(), 'a', 'dcat:Dataset');
        $graph->addResource($this->getUrl(), 'a', 'dcat:Resource');

        $graph = $this->addMetadataToGraph($metadata, $graph);

        foreach ($metadata->getThemes() as $theme) {
            $graph->addResource($this->getUrl(), 'dcat:theme', $theme->getUrl()->getValue());
        }

        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'a', 'ldp:DirectContainer');
        $graph->addLiteral($this->baseUrl . Distribution::URL_PATH, 'dcterms:title', 'Distributions');
        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:hasMemberRelation', 'dcat:distribution');
        $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:membershipResource', $this->getUrl());

        foreach ($this->dataset->getDistributions() as $distribution) {
            /** @var Distribution $distribution */
            $graph->addResource($this->getUrl(), 'dcat:distribution', $this->baseUrl . $distribution->getRelativeUrl());

            $graph->addResource($this->baseUrl . Distribution::URL_PATH, 'ldp:contains', $this->baseUrl . $distribution->getRelativeUrl());
        }

        foreach ($this->dataset->getCatalogs() as $catalog) {
            $graph->addResource($this->getUrl(), 'dcterms:isPartOf', $this->baseUrl . $catalog->getRelativeUrl());
        }

        if ($metadata->getKeyword() !== null) {
            foreach ($metadata->getKeyword()->getTexts() as $text) {
                /** @var LocalizedTextItem $text */
                $graph->addLiteral($this->getUrl(), 'dcat:keyword', $text->getText(), $text->getLanguage()->getCode());
            }
        }

        // accessRights

        return $graph;
    }
}
