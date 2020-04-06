<?php
declare(strict_types=1);

namespace App\Entity\Castor\Form;

class FieldOptionGroup
{
    /** @var string|null */
    private $id;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $description;

    /** @var string|null */
    private $layout;

    /** @var FieldOption[]|null */
    private $options;

    /**
     * @param FieldOption[]|null $options
     */
    public function __construct(?string $id, ?string $name, ?string $description, ?string $layout, ?array $options)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->layout = $layout;
        $this->options = $options;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getLayout(): ?string
    {
        return $this->layout;
    }

    public function setLayout(?string $layout): void
    {
        $this->layout = $layout;
    }

    /**
     * @return FieldOption[]|null
     */
    public function getOptions(): ?array
    {
        return $this->options;
    }

    /**
     * @param FieldOption[]|null $options
     */
    public function setOptions(?array $options): void
    {
        $this->options = $options;
    }

    public function setOption(int $pos, FieldOption $option): void
    {
        $this->options[$pos] = $option;
    }

    public function setOptionParent(): void
    {
        foreach ($this->options as $option) {
            $option->setOptionGroup($this);
        }
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): FieldOptionGroup
    {
        $options = [];
        if (isset($data['options'])) {
            foreach ($data['options'] as $option) {
                $options[] = FieldOption::fromData($option);
            }
        }
        $group = new FieldOptionGroup(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['description'] ?? null,
            $data['layout'] ?? null,
            $options
        );

        $group->setOptionParent();

        return $group;
    }
}
