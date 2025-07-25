<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

class FieldDependency
{
    public function __construct(private ?string $id = null, private ?string $parentId = null, private ?string $value = null, private ?string $description = null)
    {
    }

    public function getId(): ?string
    {
        return (string) $this->id;
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

    /** @param array<mixed> $data */
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
