<?php
declare(strict_types=1);

namespace App\Message\Agent;

use App\Entity\Study;

class AddStudyContactCommand
{
    /** @var Study */
    private $study;

    /** @var string|null */
    private $id;

    /** @var string */
    private $firstName;

    /** @var string|null */
    private $middleName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $email;

    /** @var string|null */
    private $orcid;

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
        return $this->id;
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
