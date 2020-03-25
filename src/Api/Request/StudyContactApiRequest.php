<?php

namespace App\Api\Request;

use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Country;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class StudyContactApiRequest extends GroupedApiRequest
{
    /** @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $firstName;

    /** @var string
     *
     * @Assert\Type("string")
     */
    private $middleName;

    /** @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $lastName;

    /** @var string|null
     *
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /** @var string|null
     *
     * @Assert\Type("string")
     */
    private $orcid;

    protected function parse(): void
    {
        $this->firstName = $this->getFromData('firstName');
        $this->middleName = $this->getFromData('middleName');
        $this->lastName = $this->getFromData('lastName');
        $this->email = $this->getFromData('email');
        $this->orcid = $this->getFromData('orcid');
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getMiddleName(): string
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
     * @return string|null
     */
    public function getEmail(): ?string
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