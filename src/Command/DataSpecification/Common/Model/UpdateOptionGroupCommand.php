<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

abstract class UpdateOptionGroupCommand
{
    /** @param array<array{id: string|null, title: string, description: string|null, value: string, order: int|null}> $options */
    public function __construct(private string $title, private ?string $description = null, private array $options)
    {
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
