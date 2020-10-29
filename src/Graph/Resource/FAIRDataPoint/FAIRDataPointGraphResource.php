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

        $graph = $this->addMetadataToGraph($metadata, $graph);

        foreach ($this->fairDataPoint->getCatalogs() as $catalog) {
            /** @var Catalog $catalog */
            $graph->addResource($this->getUrl(), 'r3d:dataCatalog', $this->baseUrl . $catalog->getRelativeUrl());
        }

        return $graph;
    }
}
