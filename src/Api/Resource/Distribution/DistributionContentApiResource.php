<?php
declare(strict_types=1);

namespace App\Api\Resource\Distribution;

use App\Api\Resource\ApiResource;
use App\Entity\Data\CSV\CSVDistribution;
use App\Entity\Data\CSV\CSVDistributionElementFieldId;
use App\Entity\Data\CSV\CSVDistributionElementVariableName;
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
        if($this->distribution->getContents() === null) {
            return [];
        }

        $data = [
            'accessRights' => $this->distribution->getContents()->getAccessRights(),
        ];

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
