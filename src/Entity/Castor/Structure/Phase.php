<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

use App\Entity\Castor\Structure\Step\Step;
use function uasort;

class Phase extends StructureElement
{
    /** @var string|null */
    private $description;

    /** @var string|null */
    private $name;

    /** @var int|null */
    private $position;

    public function __construct(?string $id, ?string $description, ?string $name, ?int $position)
    {
        parent::__construct();

        $this->id = $id;
        $this->description = $description;
        $this->name = $name;
        $this->position = $position;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
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

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Phase
    {
        return new Phase(
            $data['id'] ?? null,
            $data['phase_description'] ?? null,
            $data['phase_name'] ?? null,
            $data['phase_order'] ?? null
        );
    }
}
