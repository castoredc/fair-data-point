<?php
declare(strict_types=1);

namespace App\Message\Data;

class CreateDataModelCommand
{
    /** @var string */
    private $title;

    /** @var string|null */
    private $description;

    public function __construct(string $title, ?string $description)
    {
        $this->title = $title;
        $this->description = $description;
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
