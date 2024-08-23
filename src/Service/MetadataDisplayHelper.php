<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;
use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\Enum\ResourceType;
use App\Entity\Metadata\Metadata;
use function json_decode;

class MetadataDisplayHelper
{
    public static function getValueForDisplay(
        Metadata $metadata,
        MetadataModelDisplaySetting $displaySetting,
    ): mixed {
        $value = $metadata->getValueForNode($displaySetting->getNode());

        $field = $displaySetting->getNode()->getField();
        $optionGroup = $field->getOptionGroup() ?? null;

        $value = $value !== null ? json_decode($value->getValue(), true) : null;

        if ($value !== null && $optionGroup !== null) {
            $value = $optionGroup->getOption($value)->getTitle();
        }

        return $value ?? null;
    }

    /** @return array<string, MetadataModelField[]> */
    public static function getFieldsForResource(MetadataModelVersion $version, ResourceType $resourceType): array
    {
        $forms = $version->getForms()->filter(static function (MetadataModelForm $form) use ($resourceType) {
            return $resourceType->isEqualTo($form->getResourceType());
        });

        return $forms->map(static function (MetadataModelForm $form) use ($resourceType) {
            return $form->getFields()->filter(static function (MetadataModelField $field) use ($resourceType) {
                return $resourceType->isEqualTo($field->getResourceType());
            })->toArray();
        })->toArray();
    }
}
