<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Structure\Step\Step;

class StepApiResource implements ApiResource
{
    public function __construct(private Step $step)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->step->getId(),
            'position' => $this->step->getPosition(),
            'name' => $this->step->getName(),
            'description' => $this->step->getDescription(),
        ];
    }
}
