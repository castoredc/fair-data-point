<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\MetadataModel;

use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Entity\Enum\ResourceType;
use function sprintf;

class MetadataModelFormApiResource
{
    private MetadataModelForm $form;

    public function __construct(MetadataModelForm $form)
    {
        $this->form = $form;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $fields = [];

        foreach ($this->form->getFields() as $field) {
            $fields[] = (new MetadataModelFieldApiResource($field))->toArray();
        }

        $return = [
            'id' => $this->form->getId(),
            'title' => $this->form->getTitle(),
            'displayName' => sprintf('%d. %s', $this->form->getOrder(), $this->form->getTitle()),
            'order' => $this->form->getOrder(),
            'fields' => $fields,
        ];

        foreach (ResourceType::TYPES as $TYPE) {
            $return['resourceTypes'][$TYPE] = $this->form->hasResourceType(ResourceType::fromString($TYPE));
        }

        return $return;
    }
}
