<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\StructureElement;

class StructureElementApiResource implements ApiResource
{
    private StructureElement $element;

    public function __construct(StructureElement $element)
    {
        $this->element = $element;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->element->getId(),
            'name' => $this->element->getName(),
            'steps' => (new StepsApiResource($this->element->getSteps()))->toArray(),
        ];
    }
}
