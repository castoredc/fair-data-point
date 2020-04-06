<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\Structure\Step\Step;
use App\Entity\Castor\Study;

abstract class StructureElement
{
    /** @var string|null */
    protected $id;

    /** @var Step[] */
    protected $steps;

    /** @var Study|null */
    protected $study;

    public function __construct()
    {
        $this->steps = [];
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(?Study $study): void
    {
        $this->study = $study;
    }

    public function addStep(Step $step): void
    {
        $this->steps[] = $step;
    }

    /**
     * @return Step[]
     */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /**
     * @param Step[] $steps
     */
    public function setSteps(array $steps): void
    {
        $this->steps = $steps;
    }

    public function setStep(int $pos, Step $step): void
    {
        $this->steps[$pos] = $step;
    }

    public function setStepParent(): void
    {
        foreach ($this->steps as $step) {
            $step->setParent($this);
        }
    }

    public function getStep(string $id): ?Step
    {
        foreach ($this->steps as $step) {
            if ($step->getId() === $id) {
                return $step;
            }
        }

        return null;
    }

    public function orderFieldsInSteps(): void
    {
        foreach ($this->steps as $step) {
            $step->orderFields();
        }
    }
}
