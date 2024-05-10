<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelField;
use App\Entity\Enum\ResourceType;
use function sprintf;

class MetadataModelFieldApiResource
{
    public function __construct(private MetadataModelField $field)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $return = [
            'id' => $this->field->getId(),
            'title' => $this->field->getTitle(),
            'displayName' => sprintf('%d. %s', $this->field->getOrder(), $this->field->getTitle()),
            'order' => $this->field->getOrder(),
            'description' => $this->field->getDescription(),
            'node' => $this->field->getNode()->getId(),
            'resourceTypes' => [],
            'fieldType' => $this->field->getFieldType()->toString(),
            'optionGroup' => $this->field->getOptionGroup()?->getId(),
        ];

        foreach (ResourceType::TYPES as $TYPE) {
            $return['resourceTypes'][$TYPE] = $this->field->hasResourceType(ResourceType::fromString($TYPE));
        }

        return $return;
    }
}
