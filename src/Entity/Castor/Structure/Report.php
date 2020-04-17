<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure;

class Report extends StructureElement
{
    /** @var string|null */
    private $description;

    /** @var string|null */
    private $type;

    public function __construct(?string $id, ?string $name, ?string $description, ?string $type)
    {
        parent::__construct($id, $name);

        $this->description = $description;
        $this->type = $type;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Report
    {
        return new Report(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['type'] ?? null
        );
    }
}
