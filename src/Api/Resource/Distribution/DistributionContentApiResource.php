<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Distribution\CSVDistribution\CSVDistribution;
use App\Entity\FAIRData\Distribution\CSVDistribution\CSVDistributionElementFieldId;
use App\Entity\FAIRData\Distribution\CSVDistribution\CSVDistributionElementVariableName;
use App\Entity\FAIRData\Distribution\Distribution;

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
        $data = [];

        if ($this->distribution instanceof CSVDistribution) {
            $data['includeAll'] = $this->distribution->isIncludeAll();

            $elements = [];

            foreach ($this->distribution->getElements() as $element) {
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

        return $data;
    }
}
