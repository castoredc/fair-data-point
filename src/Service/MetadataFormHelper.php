<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\RenderedMetadataModelForm;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use Doctrine\Common\Util\ClassUtils;

class MetadataFormHelper
{
    /** @return RenderedMetadataModelForm[] */
    public static function getFormsForEntity(MetadataModelVersion $version, MetadataEnrichedEntity $entity): array
    {
        $forms = $version->getForms()->filter(static function (MetadataModelForm $form) use ($entity) {
            return ClassUtils::getClass($entity) === $form->getResourceType()->getClass();
        });

        $forms = $forms->map(static function (MetadataModelForm $form) use ($entity) {
            return new RenderedMetadataModelForm(
                $form,
                $form->getFields()->filter(static function (MetadataModelField $field) use ($entity) {
                    return ClassUtils::getClass($entity) === $field->getResourceType()->getClass();
                })->toArray()
            );
        });

        return $forms->toArray();
    }

    public static function getValueForField(
        MetadataModelVersion $getMetadataModelVersion,
        MetadataEnrichedEntity $getEntity,
        MetadataModelField $field,
    ): mixed {
        return $field->getFieldType()->getDefaultValue();
    }
}
