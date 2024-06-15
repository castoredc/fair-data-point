<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\Step\Step;

class StepsApiResource implements ApiResource
{
    /** @param Step[] $steps */
    public function __construct(private array $steps)
    {
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
