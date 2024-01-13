<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Study;

class AddStudyContactCommand
{
    private Study $study;

    private ?string $id = null;

    private string $firstName;

    private ?string $middleName = null;

    private string $lastName;

    private string $email;

    private ?string $orcid = null;

    public function __construct(Study $study, ?string $id, string $firstName, ?string $middleName, string $lastName, string $email, ?string $orcid)
    {
        $this->study = $study;
        $this->id = $id;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->orcid = $orcid;
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
