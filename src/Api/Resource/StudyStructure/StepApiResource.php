<?php

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\Step\Step;
use App\Entity\Castor\Structure\StructureElement;

class StepApiResource implements ApiResource
{
    /** @var Step */
    private $step;

    public function __construct(Step $step)
    {
        $this->step = $step;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->step->getId(),
            'name' => $this->step->getName(),
            'description' => $this->step->getDescription(),
        ];
    }
}