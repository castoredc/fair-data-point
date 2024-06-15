<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\Common\Model;

abstract class UpdateModelCommand
{
    public function __construct(private string $title, private ?string $description = null)
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
}
