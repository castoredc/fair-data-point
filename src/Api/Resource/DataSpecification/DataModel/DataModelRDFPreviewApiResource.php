<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationEdgeApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationNodeApiResource;
use App\Entity\DataSpecification\DataModel\Triple;
use function array_values;

class DataModelRDFPreviewApiResource implements ApiResource
{
    /** @var Triple[] */
    private array $triples;

    /** @var DataModelModuleRDFPreviewApiResource[] */
    private array $modulePreviews;

    private string $rdfPreview;

    /**
     * @param Triple[]                               $triples
     * @param DataModelModuleRDFPreviewApiResource[] $modulePreviews
     */
    public function __construct(array $triples, array $modulePreviews, string $rdfPreview)
    {
        $this->triples = $triples;
        $this->modulePreviews = $modulePreviews;
        $this->rdfPreview = $rdfPreview;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->modulePreviews as $modulePreview) {
            $data[] = $modulePreview->toArray();
        }

        $visualizationEdges = [];
        $visualizationNodes = [];

        foreach ($this->triples as $triple) {
            /** @var Triple $triple */
            $visualizationNodes[$triple->getSubject()->getId()] = (new VisualizationNodeApiResource($triple->getSubject()))->toArray();
            $visualizationNodes[$triple->getObject()->getId()] = (new VisualizationNodeApiResource($triple->getObject()))->toArray();

            $visualizationEdges[] = (new VisualizationEdgeApiResource($triple))->toArray();
        }

        return [
            'modules' => $data,
            'full' => $this->rdfPreview,
            'visualization' => [
                'edges' => $visualizationEdges,
                'nodes' => array_values($visualizationNodes),
            ],
        ];
    }
}
