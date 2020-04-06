<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

class FieldOption
{
    /** @var string|null */
    private $id;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $value;

    /** @var int|null */
    private $groupOrder;

    /** @var FieldOptionGroup */
    private $optionGroup;

    public function __construct(?string $id, ?string $name, ?string $value, ?int $groupOrder)
    {
        $this->id = $id;
        $this->name = $name;
        $this->value = $value;
        $this->groupOrder = $groupOrder;
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

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getGroupOrder(): ?int
    {
        return $this->groupOrder;
    }

    public function setGroupOrder(?int $groupOrder): void
    {
        $this->groupOrder = $groupOrder;
    }

    public function getOptionGroup(): FieldOptionGroup
    {
        return $this->optionGroup;
    }

    public function setOptionGroup(FieldOptionGroup $optionGroup): void
    {
        $this->optionGroup = $optionGroup;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): FieldOption
    {
        return new FieldOption(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['value'] ?? null,
            $data['groupOrder'] ?? null
        );
    }
}
