<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\RenderedMetadataModelForm;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Metadata\Metadata;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Symfony\Component\Validator\Constraints as Assert;
use function array_merge;
use function get_parent_class;
use function is_int;
use function json_decode;

class MetadataFormHelper
{
    /** @return Collection<RenderedMetadataModelForm> */
    public static function getFormsForEntity(MetadataModelVersion $version, MetadataEnrichedEntity $entity): Collection
    {
        $forms = $version->getForms()->filter(static function (MetadataModelForm $form) use ($entity) {
            return ClassUtils::getClass($entity) === $form->getResourceType()->getClass() ||
                get_parent_class(ClassUtils::getClass($entity)) === $form->getResourceType()->getClass();
        });

        return $forms->map(static function (MetadataModelForm $form) use ($entity) {
            return new RenderedMetadataModelForm(
                $form,
                $form->getFields()->filter(static function (MetadataModelField $field) use ($entity) {
                    return ClassUtils::getClass($entity) === $field->getResourceType()->getClass() ||
                        get_parent_class(ClassUtils::getClass($entity)) === $field->getResourceType()->getClass();
                })->toArray()
            );
        });
    }

    /** @return MetadataModelField[] */
    public static function getFieldsForEntity(MetadataModelVersion $version, MetadataEnrichedEntity $entity): array
    {
        $forms = self::getFormsForEntity($version, $entity);

        return array_merge(...$forms->map(static function (RenderedMetadataModelForm $form) {
            return $form->getFields();
        })->toArray());
    }

    /** @return array<string, array<int, object>> */
    public static function getValidatorsForEntity(MetadataModelVersion $version, MetadataEnrichedEntity $entity): array
    {
        $fields = self::getFieldsForEntity($version, $entity);
        $validators = [];

        foreach ($fields as $field) {
            $validator = [];

            $validationInfo = $field->getFieldType()->getValidator();

            if ($validationInfo === null) {
                continue;
            }

            if ($validationInfo['app'] === true) {
                $validator[] = new $validationInfo['type']();
            } else {
                $validator[] = new Assert\Type($validationInfo['type']);
            }

            if ($field->isRequired()) {
                $validator[] = new Assert\NotBlank();
            }

            $validators[$field->getId()] = $validator;
        }

        return $validators;
    }

    public static function getValueForField(
        Metadata $metadata,
        MetadataModelField $field,
    ): mixed {
        $value = $metadata->getValueForNode($field->getNode());
        $value = $value !== null ? json_decode($value->getValue(), true) : null;
        $value ??= $field->getFieldType()->getDefaultValue();

        return is_int($value) ? (string) $value : $value;
    }
}
