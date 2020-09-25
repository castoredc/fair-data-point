<?php
declare(strict_types=1);

namespace App\Api\Resource\Data;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Data\Visualization\VisualizationEdgeApiResource;
use App\Api\Resource\Data\Visualization\VisualizationNodeApiResource;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Triple;
use function array_values;

class DataModelModuleRDFPreviewApiResource implements ApiResource
{
    /** @var DataModelModule */
    private $module;

    /** @var string */
    private $rdfPreview;

    public function __construct(DataModelModule $module, string $rdfPreview)
    {
        $this->module = $module;
        $this->rdfPreview = $rdfPreview;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $visualizationEdges = [];
        $visualizationNodes = [];

        foreach ($this->module->getTriples() as $triple) {
            /** @var Triple $triple */
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
