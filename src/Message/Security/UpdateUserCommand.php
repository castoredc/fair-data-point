<?php
declare(strict_types=1);

namespace App\Message\Security;

class UpdateUserCommand
{
    private string $firstName;
    private ?string $middleName = null;
    private string $lastName;
    private string $email;

    public function __construct(string $firstName, ?string $middleName, string $lastName, string $email)
    {
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->email = $email;
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
