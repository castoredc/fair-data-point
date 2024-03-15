<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

abstract class UpdateOptionGroupCommand
{
    private string $title;

    private ?string $description = null;

    /** @var array<array{id: string|null, title: string, description: string|null, value: string, order: int|null}> */
    private array $options;

    /** @param array<array{id: string|null, title: string, description: string|null, value: string, order: int|null}> $options */
    public function __construct(string $title, ?string $description, array $options)
    {
        $this->title = $title;
        $this->description = $description;
        $this->options = $options;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /** @return array<array{id: string|null, title: string, description: string|null, value: string, order: int|null}> */
    public function getOptions(): array
    {
        return $this->options;
    }
}
