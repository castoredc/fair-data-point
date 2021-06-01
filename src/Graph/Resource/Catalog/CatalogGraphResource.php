<?php
declare(strict_types=1);

namespace App\Graph\Resource\Catalog;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Graph\Resource\GraphResource;
use EasyRdf\Graph;

class CatalogGraphResource extends GraphResource
{
    private Catalog $catalog;

    public function __construct(Catalog $catalog, string $baseUrl)
    {
        $this->catalog = $catalog;
        parent::__construct($catalog, $baseUrl);
    }

    public function toGraph(): Graph
    {
        $graph = new Graph();
        $metadata = $this->catalog->getLatestMetadata();
        $fdp = $this->catalog->getFairDataPoint();

        $graph->addResource($this->getUrl(), 'a', 'dcat:Catalog');
        $graph->addResource($this->getUrl(), 'a', 'dcat:Resource');

        $graph = $this->addMetadataToGraph($metadata, $graph);

        foreach ($metadata->getThemeTaxonomies() as $themeTaxonomy) {
            $graph->addResource($this->getUrl(), 'dcat:themeTaxonomy', $themeTaxonomy->getUrl()->getValue());
        }

        $graph->addResource($this->baseUrl . Dataset::URL_PATH, 'a', 'ldp:DirectContainer');
        $graph->addLiteral($this->baseUrl . Dataset::URL_PATH, 'dcterms:title', 'Datasets');
        $graph->addResource($this->baseUrl . Dataset::URL_PATH, 'ldp:hasMemberRelation', 'dcat:dataset');
        $graph->addResource($this->baseUrl . Dataset::URL_PATH, 'ldp:membershipResource', $this->getUrl());

        foreach ($this->catalog->getDatasets(false) as $dataset) {
            $graph->addResource($this->getUrl(), 'dcat:dataset', $this->baseUrl . $dataset->getRelativeUrl());

            $graph->addResource($this->baseUrl . Dataset::URL_PATH, 'ldp:contains', $this->baseUrl . $dataset->getRelativeUrl());
        }

        $graph->addResource($this->getUrl(), 'dcterms:isPartOf', $this->baseUrl . $fdp->getRelativeUrl());

        // accessRights
        // studies

        return $graph;
    }
}
