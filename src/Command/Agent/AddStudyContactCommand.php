<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Study;

class AddStudyContactCommand
{
    public function __construct(private Study $study, private string $firstName, private string $lastName, private string $email, private ?string $id = null, private ?string $middleName = null, private ?string $orcid = null)
    {
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getId(): ?string
    {
        return (string) $this->id;
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getOrcid(): ?string
    {
        return $this->orcid;
    }
}
