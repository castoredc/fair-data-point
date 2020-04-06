<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

class FieldDependency
{
    /** @var string|null */
    private $id;

    /** @var string|null */
    private $parentId;

    /** @var string|null */
    private $value;

    /** @var string|null */
    private $description;

    public function __construct(?string $id, ?string $parentId, ?string $value, ?string $description)
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->value = $value;
        $this->description = $description;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function setParentId(?string $parentId): void
    {
        $this->parentId = $parentId;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): FieldDependency
    {
        return new FieldDependency(
            $data['id'] ?? null,
            $data['parentId'] ?? null,
            $data['value'] ?? null,
            $data['description'] ?? null
        );
    }
}
