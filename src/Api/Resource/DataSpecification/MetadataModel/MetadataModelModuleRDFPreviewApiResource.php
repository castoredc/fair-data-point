<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationEdgeApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationNodeApiResource;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use function array_values;

class MetadataModelModuleRDFPreviewApiResource implements ApiResource
{
    public function __construct(private MetadataModelGroup $module, private string $rdfPreview)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $visualizationEdges = [];
        $visualizationNodes = [];

        foreach ($this->module->getTriples() as $triple) {
            $visualizationNodes[$triple->getSubject()->getId()] = (new VisualizationNodeApiResource($triple->getSubject()))->toArray();
            $visualizationNodes[$triple->getObject()->getId()] = (new VisualizationNodeApiResource($triple->getObject()))->toArray();

            $visualizationEdges[] = (new VisualizationEdgeApiResource($triple))->toArray();
        }

        $array = (new MetadataModelModuleApiResource($this->module))->toArray();
        $array['rdf'] = $this->rdfPreview;
        $array['visualization'] = [
            'edges' => $visualizationEdges,
            'nodes' => array_values($visualizationNodes),
        ];

        return $array;
    }
}
