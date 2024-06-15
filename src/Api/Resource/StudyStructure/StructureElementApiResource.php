<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\StructureElement;

class StructureElementApiResource implements ApiResource
{
    public function __construct(private StructureElement $element)
    {
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
