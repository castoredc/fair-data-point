<?php
declare(strict_types=1);

namespace App\Command\Castor;

class ImportStudyCommand
{
    public function __construct(private string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
