<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\DynamicApiRequest;
use App\Entity\Metadata\Metadata;
use App\Service\MetadataFormHelper;
use Symfony\Component\Validator\Constraints as Assert;
use function assert;

class MetadataValuesApiRequest extends DynamicApiRequest
{
    public function getConstraints(): Assert\Collection
    {
        $metadata = $this->getContext();
        assert($metadata instanceof Metadata);

        $metadataModelVersion = $metadata->getMetadataModelVersion();

        return new Assert\Collection(
            [
                'allowExtraFields' => true,
                'fields' => MetadataFormHelper::getValidatorsForEntity($metadataModelVersion, $metadata->getEntity()),
            ]
        );
    }

    /** @return array<string, mixed> */
    public function getValues(): array
    {
        $metadata = $this->getContext();

        assert($metadata instanceof Metadata);

        $metadataModelVersion = $metadata->getMetadataModelVersion();
        $fields = $metadataModelVersion->getFields();

        $fieldValues = [];

        foreach ($fields as $field) {
            $fieldValues[$field->getId()] = $this->getFromData($field->getId());
        }

        return $fieldValues;
    }

    protected function parse(): void
    {
    }
}
