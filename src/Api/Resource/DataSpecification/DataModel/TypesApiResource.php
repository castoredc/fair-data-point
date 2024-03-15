<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\DataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Enum\XsdDataType;

class TypesApiResource implements ApiResource
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [
            'fieldTypes' => [],
            'dataTypes' => [],
        ];

        foreach (XsdDataType::ANY_TYPES as $dataType) {
            $data['dataTypes'][] = [
                'label' => XsdDataType::LABELS[$dataType],
                'value' => $dataType,
            ];
        }

        return $data;
    }
}
