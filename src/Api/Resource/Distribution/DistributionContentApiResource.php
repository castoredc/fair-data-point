<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Data\DataModelApiResource;
use App\Entity\Data\CSV\CSVDistributionElementFieldId;
use App\Entity\Data\CSV\CSVDistributionElementVariableName;
use App\Entity\Data\DistributionContents\CSVDistribution;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\FAIRData\Distribution;

class DistributionContentApiResource implements ApiResource
{
    private Distribution $distribution;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        if ($this->distribution->getContents() === null) {
            return [];
        }

        $contents = $this->distribution->getContents();

        $data = [
            'dependencies' => $contents->getDependencies() !== null ? (new DistributionContentsDependencyApiResource($contents->getDependencies()))->toArray() : null,
        ];

        if ($contents instanceof CSVDistribution) {
            $elements = [];

            foreach ($contents->getElements() as $element) {
                if ($element instanceof CSVDistributionElementFieldId) {
                    $elements[] = [
                        'type' => 'fieldId',
                        'value' => $element->getFieldId(),
                    ];
                } elseif ($element instanceof CSVDistributionElementVariableName) {
                    $elements[] = [
                        'type' => 'variableName',
                        'value' => $element->getVariableName(),
                    ];
                }
            }

            $data['elements'] = $elements;
        }

        if ($contents instanceof RDFDistribution) {
            $elements = [];

            $data['dataModel'] = (new DataModelApiResource($contents->getDataModel()))->toArray();

            // foreach ($contents->getModules() as $module) {
            //     /** @var RDFDistributionModule $module */
            //     $triples = [];
            //     foreach ($module->getTriples() as $triple) {
            //         /** @var RDFTriple $triple */
            //         $triples[] = (new RDFTripleApiResource($triple))->toArray();
            //     }
            //
            //     $elements[] = [
            //         'id' => $module->getId(),
            //         'title' => $module->getTitle(),
            //         'order' => $module->getOrder(),
            //         'triples' => $triples,
            //     ];
            // }
            //
            // $data['elements'] = $elements;
        }

        return $data;
    }
}
