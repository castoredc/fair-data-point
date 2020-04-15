<?php

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\StructureElement;

class StructureElementApiResource implements ApiResource
{
    /** @var StructureElement */
    private $element;

    public function __construct(StructureElement $element)
    {
        $this->element = $element;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->element->getId(),
            'name' => $this->element->getName(),
            'steps' => (new StepsApiResource($this->element->getSteps()))->toArray()
        ];
    }
}