<?php
declare(strict_types=1);

namespace App\Graph\Resource\Distribution;

use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Distribution;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

class DistributionGraphResource extends GraphResource
{
    private Distribution $distribution;

    public function __construct(Distribution $distribution, string $baseUrl)
    {
        $this->distribution = $distribution;
        parent::__construct($distribution, $baseUrl);
    }

    public function toGraph(): Graph
    {
        $graph = new Graph();
        $metadata = $this->distribution->getLatestMetadata();

        $graph->addResource($this->getUrl(), 'a', 'dcat:Distribution');

        $graph = $this->addMetadataToGraph($metadata, $graph);

        $contents = $this->distribution->getContents();
        $dataset = $this->distribution->getDataset();

        if ($contents instanceof RDFDistribution) {
            $graph->addResource($this->getUrl(), 'dcat:accessURL', $this->baseUrl . $contents->getRelativeUrl());
            $graph->addLiteral($this->getUrl(), 'dcat:mediaType', 'text/turtle');
        }

        if ($contents instanceof CSVDistribution) {
            $graph->addResource($this->getUrl(), 'dcat:accessURL', $this->baseUrl . $contents->getRelativeUrl());
            $graph->addLiteral($this->getUrl(), 'dcat:mediaType', 'text/csv');
        }

        $graph->addResource($this->getUrl(), 'dcterms:isPartOf', $this->baseUrl . $dataset->getRelativeUrl());

        return $graph;
    }
}
