<?php
declare(strict_types=1);

namespace App\Graph\Resource\Dataset;

use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

class DatasetGraphResource extends GraphResource
{
    private Dataset $dataset;

    public function __construct(Dataset $dataset, string $baseUrl)
    {
        $this->dataset = $dataset;
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

        $graph = $this->addMetadataToGraph($metadata, $graph);

        foreach ($this->dataset->getThemes() as $theme) {
            $graph->addResource($this->getUrl(), 'dcat:theme', $theme->getUrl()->getValue());
        }

        foreach ($this->dataset->getDistributions() as $distribution) {
            /** @var Distribution $distribution */
            $graph->addResource($this->getUrl(), 'dcat:distribution', $this->baseUrl . $distribution->getRelativeUrl());
        }

        foreach ($this->dataset->getCatalogs() as $catalog) {
            $graph->addResource($this->getUrl(), 'dcterms:isPartOf', $this->baseUrl . $catalog->getRelativeUrl());
        }

        return $graph;
    }
}
