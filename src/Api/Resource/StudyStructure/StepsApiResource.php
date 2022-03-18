<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\Step\Step;

class StepsApiResource implements ApiResource
{
    /** @var Step[] */
    private array $steps;

    /** @param Step[] $steps */
    public function __construct(array $steps)
    {
        $this->steps = $steps;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->steps as $step) {
            $data[] = (new StepApiResource($step))->toArray();
        }

        return $data;
    }
}
