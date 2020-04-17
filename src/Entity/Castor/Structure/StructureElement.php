<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\Structure\Step\Step;
use App\Entity\Castor\Study;

abstract class StructureElement
{
    /** @var string|null */
    protected $id;

    /** @var string|null */
    protected $name;

    /** @var Step[] */
    protected $steps;

    /** @var Study|null */
    protected $study;

    public function __construct(?string $id, ?string $name)
    {
        $this->id = $id;
        $this->name = $name;
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

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
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
}
