<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Person extends Agent
{
    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $middleName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $lastName;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $orcid;

    public function __construct(string $firstName, ?string $middleName, string $lastName, string $email, ?string $phoneNumber, ?Iri $orcid)
    {
        $slugify = new Slugify();

        $fullName = $middleName !== null ? $firstName . ' ' . $middleName . ' ' . $lastName : $firstName . ' ' . $lastName;
        parent::__construct($slugify->slugify($fullName), $fullName);

        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->orcid = $orcid;
    }

    public function getAccessUrl(): string
    {
        return '/agent/person/' . $this->getSlug();
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): void
    {
        $this->middleName = $middleName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getOrcid(): ?Iri
    {
        return $this->orcid;
    }

    public function setOrcid(?Iri $orcid): void
    {
        $this->orcid = $orcid;
    }
}
