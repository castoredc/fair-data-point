<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Enum\MetadataFieldType;
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

        foreach (MetadataFieldType::PLAIN_VALUE_TYPES as $dataType => $fieldTypes) {
            foreach ($fieldTypes as $fieldType) {
                $data['fieldTypes'][MetadataFieldType::TYPE_PLAIN][$dataType][] = [
                    'label' => MetadataFieldType::LABELS[$fieldType],
                    'value' => $fieldType,
                ];
            }
        }

        foreach (MetadataFieldType::ANNOTATED_VALUE_TYPES as $fieldType) {
            $data['fieldTypes'][MetadataFieldType::TYPE_ANNOTATED][] = [
                'label' => MetadataFieldType::LABELS[$fieldType],
                'value' => $fieldType,
            ];
        }

        foreach (XsdDataType::ANY_TYPES as $dataType) {
            $data['dataTypes'][] = [
                'label' => XsdDataType::LABELS[$dataType],
                'value' => $dataType,
            ];
        }

        return $data;
    }
}
