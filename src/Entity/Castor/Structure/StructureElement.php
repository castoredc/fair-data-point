<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Structure\Step\Step;
use App\Entity\Enum\StructureType;
use Doctrine\ORM\Mapping as ORM;

/** @ORM\Entity */
abstract class StructureElement extends CastorEntity
{
    protected ?string $name = null;

    /** @var Step[] */
    protected array $steps;

    public function __construct(string $id, CastorStudy $study, StructureType $structureType, string $name)
    {
        parent::__construct($id, $name, $study, $structureType);

        $this->name = $name;
        $this->steps = [];
    }

    public function addStep(Step $step): void
    {
        $this->steps[] = $step;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /** @return Step[] */
    public function getSteps(): array
    {
        return $this->steps;
    }

    /** @param Step[] $steps */
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
