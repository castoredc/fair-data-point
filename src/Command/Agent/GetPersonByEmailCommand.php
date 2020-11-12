<?php
declare(strict_types=1);

namespace App\Command\Agent;

class GetPersonByEmailCommand
{
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}
