<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\CSV\CSVDistributionElementFieldId;
use App\Entity\Data\CSV\CSVDistributionElementVariableName;
use App\Entity\Data\RDF\RDFDistribution;
use App\Entity\FAIRData\Distribution;

class DistributionContentApiResource implements ApiResource
{
    /** @var Distribution */
    private $distribution;

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

        $data = [
            'accessRights' => $this->distribution->getContents()->getAccessRights(),
        ];

        $contents = $this->distribution->getContents();

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
