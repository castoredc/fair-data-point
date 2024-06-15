<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationEdgeApiResource;
use App\Api\Resource\DataSpecification\Visualization\VisualizationNodeApiResource;
use App\Entity\DataSpecification\MetadataModel\Triple;
use function array_values;

class MetadataModelRDFPreviewApiResource implements ApiResource
{
    /**
     * @param Triple[]                                   $triples
     * @param MetadataModelModuleRDFPreviewApiResource[] $modulePreviews
     */
    public function __construct(private array $triples, private array $modulePreviews, private string $rdfPreview)
    {
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
