<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationEdgeApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationNodeApiResource;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Triple;
use function array_values;
use function assert;

class DataModelModuleRDFPreviewApiResource implements ApiResource
{
    public function __construct(private DataModelGroup $module, private string $rdfPreview)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $visualizationEdges = [];
        $visualizationNodes = [];

        foreach ($this->module->getTriples() as $triple) {
            assert($triple instanceof Triple);

            $visualizationNodes[$triple->getSubject()->getId()] = (new VisualizationNodeApiResource($triple->getSubject()))->toArray();
            $visualizationNodes[$triple->getObject()->getId()] = (new VisualizationNodeApiResource($triple->getObject()))->toArray();

            $visualizationEdges[] = (new VisualizationEdgeApiResource($triple))->toArray();
        }

        $array = (new DataModelModuleApiResource($this->module))->toArray();
        $array['rdf'] = $this->rdfPreview;
        $array['visualization'] = [
            'edges' => $visualizationEdges,
            'nodes' => array_values($visualizationNodes),
        ];

        return $array;
    }
}
