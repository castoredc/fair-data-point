<?php

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\Step\Step;
use App\Entity\Castor\Structure\StructureElement;

class StepsApiResource implements ApiResource
{
    /** @var Step[] */
    private $steps;

    /**
     * StepsApiResource constructor.
     *
     * @param Step[] $steps
     */
    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->steps as $step) {
            $data[] = (new StepApiResource($step))->toArray();
        }

        return $data;
    }
}