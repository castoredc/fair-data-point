<?php
declare(strict_types=1);

namespace App\Graph\Resource\Distribution;

use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\RDF\RDFDistribution;
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

        if ($contents instanceof RDFDistribution) {
            $graph->addResource($this->getUrl(), 'dcat:accessURL', $this->baseUrl . $contents->getRelativeUrl());
            $graph->addLiteral($this->getUrl(), 'dcat:mediaType', 'text/turtle');
        }

        if ($contents instanceof CSVDistribution) {
            $graph->addResource($this->getUrl(), 'dcat:accessURL', $this->baseUrl . $contents->getRelativeUrl());
            $graph->addLiteral($this->getUrl(), 'dcat:mediaType', 'text/csv');
        }

        return $graph;
    }
}
