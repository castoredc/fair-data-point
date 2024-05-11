<?php
declare(strict_types=1);

namespace App\Api\Resource\Metadata;

use App\Api\Resource\ApiResource;
use App\Entity\Metadata\Metadata;
use App\Service\MetadataFormHelper;
use function sprintf;

class MetadataFormsApiResource implements ApiResource
{
    public function __construct(private Metadata $metadata)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $renderedForms = MetadataFormHelper::getFormsForEntity(
            $this->metadata->getMetadataModelVersion(),
            $this->metadata->getEntity()
        );

        $return = [];

        foreach ($renderedForms as $renderedForm) {
            $fields = [];

            foreach ($renderedForm->getFields() as $field) {
                $fields[] = [
                    'id' => $field->getId(),
                    'title' => $field->getTitle(),
                    'displayName' => sprintf('%d. %s', $field->getOrder(), $field->getTitle()),
                    'order' => $field->getOrder(),
                    'description' => $field->getDescription(),
                    'fieldType' => $field->getFieldType()->toString(),
                    'optionGroup' => $field->getOptionGroup()?->getId(),
                    'isRequired' => $field->isRequired(),
                    'value' => MetadataFormHelper::getValueForField(
                        $this->metadata->getMetadataModelVersion(),
                        $this->metadata->getEntity(),
                        $field
                    ),
                ];
            }

            $return[] = [
                'id' => $renderedForm->getForm()->getId(),
                'title' => $renderedForm->getForm()->getTitle(),
                'displayName' => sprintf('%d. %s', $renderedForm->getForm()->getOrder(), $renderedForm->getForm()->getTitle()),
                'order' => $renderedForm->getForm()->getOrder(),
                'fields' => $fields,
            ];
        }

        return $return;
    }
}
