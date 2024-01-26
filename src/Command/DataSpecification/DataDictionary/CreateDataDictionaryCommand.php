<?php
declare(strict_types=1);

namespace App\Command\DataSpecification\DataDictionary;

class CreateDataDictionaryCommand
{
    private string $title;

    private ?string $description = null;

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
