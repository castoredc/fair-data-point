<?php

namespace App\Message\Api\Study;

use App\Entity\FAIRData\Country;
use App\Entity\Iri;

class CreatePersonCommand
{
    /** @var string
     */
    private $studyId;

    /** @var string
     */
    private $firstName;

    /** @var string|null
     */
    private $middleName;

    /** @var string
     */
    private $lastName;

    /** @var string
     */
    private $email;

    /** @var string|null
     */
    private $orcid;

    /**
     * CreatePersonCommand constructor.
     *
     * @param string      $studyId
     * @param string      $firstName
     * @param string|null $middleName
     * @param string      $lastName
     * @param string      $email
     * @param string|null $orcid
     */
    public function __construct(string $studyId, string $firstName, ?string $middleName, string $lastName, string $email, ?string $orcid)
    {
        $this->studyId = $studyId;
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->orcid = $orcid;
    }

    /**
     * @return string
     */
    public function getStudyId(): string
    {
        return $this->studyId;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getOrcid(): ?string
    {
        return $this->orcid;
    }
}