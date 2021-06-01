<?php
declare(strict_types=1);

namespace App\Graph\Resource\FAIRDataPoint;

use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\FAIRDataPoint;
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
        $metadata = $this->fairDataPoint->getLatestMetadata();

        $graph->addResource($this->getUrl(), 'a', 'r3d:Repository');
        $graph->addResource($this->getUrl(), 'a', 'dcat:Resource');

        $graph->addResource($this->getUrl(), 'dcterms:conformsTo', 'https://www.purl.org/fairtools/fdp/schema/0.1/fdpMetadata');

        $graph = $this->addMetadataToGraph($metadata, $graph);
        $graph->addResource($this->getUrl(), 'r3d:repositoryIdentifier', $this->getIdentifierURL());

        $graph->addResource($this->baseUrl . Catalog::URL_PATH, 'a', 'ldp:DirectContainer');
        $graph->addLiteral($this->baseUrl . Catalog::URL_PATH, 'dcterms:title', 'Catalogs');
        $graph->addResource($this->baseUrl . Catalog::URL_PATH, 'ldp:hasMemberRelation', 'r3d:dataCatalog');
        $graph->addResource($this->baseUrl . Catalog::URL_PATH, 'ldp:membershipResource', $this->getUrl());

        foreach ($this->fairDataPoint->getCatalogs() as $catalog) {
            /** @var Catalog $catalog */
            $graph->addResource($this->getUrl(), 'r3d:dataCatalog', $this->baseUrl . $catalog->getRelativeUrl());

            $graph->addResource($this->baseUrl . Catalog::URL_PATH, 'ldp:contains', $this->baseUrl . $catalog->getRelativeUrl());
        }

        // accessRights
        // institutionCountry

        return $graph;
    }
}
