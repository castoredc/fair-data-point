<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Structure\Step\Step;
use App\Entity\Enum\StructureType;
use function uasort;

class Phase extends StructureElement
{
    private ?string $description = null;

    private ?int $position = null;

    public function __construct(string $id, CastorStudy $study, ?string $name, ?string $description, ?int $position)
    {
        parent::__construct($id, $study, StructureType::study(), $name);

        $this->description = $description;
        $this->position = $position;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function orderSteps(): void
    {
        uasort($this->steps, static function (Step $a, Step $b) {
            if ($a->getPosition() === $b->getPosition()) {
                return 0;
            }

            return $a->getPosition() < $b->getPosition() ? -1 : 1;
        });
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data, CastorStudy $study): Phase
    {
        return new Phase(
            $data['id'],
            $study,
            $data['phase_name'] ?? null,
            $data['phase_description'] ?? null,
            $data['phase_order'] ?? null
        );
    }
}
