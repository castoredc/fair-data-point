<?php
declare(strict_types=1);

namespace App\Command\Agent;

class GetPersonByEmailCommand
{
    public function __construct(private string $email)
    {
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
