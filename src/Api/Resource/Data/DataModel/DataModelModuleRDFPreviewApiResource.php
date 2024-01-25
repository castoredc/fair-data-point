<?php
declare(strict_types=1);

namespace App\Api\Resource\Data\DataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Data\Visualization\VisualizationEdgeApiResource;
use App\Api\Resource\Data\Visualization\VisualizationNodeApiResource;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Triple;
use function array_values;
use function assert;

class DataModelModuleRDFPreviewApiResource implements ApiResource
{
    private DataModelGroup $module;

    private string $rdfPreview;

    public function __construct(DataModelGroup $module, string $rdfPreview)
    {
        $this->module = $module;
        $this->rdfPreview = $rdfPreview;
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
