<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Api\Resource\Terminology\AnnotationApiResource;
use App\Entity\Castor\Form\FieldOptionGroup;

class OptionGroupApiResource implements ApiResource
{
    private FieldOptionGroup $optionGroup;

    public function __construct(FieldOptionGroup $optionGroup)
    {
        $this->optionGroup = $optionGroup;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $options = [];

        foreach ($this->optionGroup->getOptions() as $option) {
            $annotations = [];

            foreach ($option->getAnnotations() as $annotation) {
                $annotations[] = (new AnnotationApiResource($annotation))->toArray();
            }

            $options[] = [
                'id' => $option->getId(),
                'value' => $option->getValue(),
                'name' => $option->getName(),
                'order' => $option->getGroupOrder(),
                'annotations' => $annotations,
            ];
        }

        return [
            'id' => $this->optionGroup->getId(),
            'name' => $this->optionGroup->getName(),
            'options' => $options,
        ];
    }
}
