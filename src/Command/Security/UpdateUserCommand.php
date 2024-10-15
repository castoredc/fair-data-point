<?php
declare(strict_types=1);

namespace App\Command\Security;

class UpdateUserCommand
{
    public function __construct(private string $firstName, private string $lastName, private string $email, private ?string $middleName = null)
    {
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }
}
