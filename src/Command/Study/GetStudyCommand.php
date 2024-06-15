<?php
declare(strict_types=1);

namespace App\Command\Study;

class GetStudyCommand
{
    public function __construct(private string $id)
    {
    }

    public function getId(): string
    {
        return $this->id;
    }
}
