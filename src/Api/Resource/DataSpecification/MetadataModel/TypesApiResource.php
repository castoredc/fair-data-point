<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Api\Resource\ApiResource;
use App\Entity\Enum\MetadataDisplayType;
use App\Entity\Enum\MetadataFieldType;
use App\Entity\Enum\XsdDataType;
use function array_unique;
use function array_values;
use const SORT_REGULAR;

class TypesApiResource implements ApiResource
{
    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [
            'fieldTypes' => [],
            'dataTypes' => [],
            'displayTypes' => [],
        ];

        foreach (MetadataFieldType::PLAIN_VALUE_TYPES as $dataType => $fieldTypes) {
            foreach ($fieldTypes as $fieldType) {
                $data['fieldTypes'][MetadataFieldType::TYPE_PLAIN][$dataType][] = [
                    'label' => MetadataFieldType::LABELS[$fieldType],
                    'value' => $fieldType,
                ];

                foreach (MetadataFieldType::DISPLAY_TYPES[$fieldType] as $displayType) {
                    $data['displayTypes'][MetadataFieldType::TYPE_PLAIN][$dataType][] = [
                        'label' => MetadataDisplayType::LABELS[$displayType],
                        'value' => $displayType,
                    ];
                }
            }

            $data['displayTypes'][MetadataFieldType::TYPE_PLAIN][$dataType] = array_unique($data['displayTypes'][MetadataFieldType::TYPE_PLAIN][$dataType], SORT_REGULAR);
        }

        foreach (MetadataFieldType::ANNOTATED_VALUE_TYPES as $fieldType) {
            $data['fieldTypes'][MetadataFieldType::TYPE_ANNOTATED][] = [
                'label' => MetadataFieldType::LABELS[$fieldType],
                'value' => $fieldType,
            ];

            foreach (MetadataFieldType::DISPLAY_TYPES[$fieldType] as $displayType) {
                $data['displayTypes'][MetadataFieldType::TYPE_ANNOTATED][] = [
                    'label' => MetadataDisplayType::LABELS[$displayType],
                    'value' => $displayType,
                ];
            }

            $data['displayTypes'][MetadataFieldType::TYPE_ANNOTATED] = array_values(array_unique($data['displayTypes'][MetadataFieldType::TYPE_ANNOTATED], SORT_REGULAR));
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
