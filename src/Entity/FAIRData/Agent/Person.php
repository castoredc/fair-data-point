<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Agent;

use App\Entity\Enum\NameOrigin;
use App\Entity\Iri;
use App\Security\User;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use function array_filter;
use function implode;
use function strtolower;

/**
 * @ORM\Entity
 */
class Person extends Agent
{
    public const TYPE = 'person';

    /** @ORM\Column(type="string") */
    private string $firstName;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $middleName = null;

    /** @ORM\Column(type="string") */
    private string $lastName;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $email = null;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $phoneNumber = null;

    /** @ORM\Column(type="iri", nullable=true) */
    private ?Iri $orcid = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="person")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private ?User $user = null;

    /** @ORM\Column(type="NameOriginType") */
    private NameOrigin $nameOrigin;

    /**
     * @ORM\OneToMany(targetEntity="Affiliation", mappedBy="person", cascade={"persist"}, fetch="EAGER")
     *
     * @var ArrayCollection<Affiliation>
     */
    private ArrayCollection $affiliations;

    public function __construct(string $firstName, ?string $middleName, string $lastName, ?string $email, ?string $phoneNumber, ?Iri $orcid, NameOrigin $nameOrigin)
    {
        $this->firstName = $firstName;
        $this->middleName = $middleName;
        $this->lastName = $lastName;
        $this->email = $email !== null ? strtolower($email) : null;
        $this->phoneNumber = $phoneNumber;
        $this->orcid = $orcid;
        $this->nameOrigin = $nameOrigin;

        $slugify = new Slugify();
        $fullName = $this->getFullName();
        parent::__construct($slugify->slugify($fullName), $fullName);

        $this->affiliations = new ArrayCollection();
    }

    public function getFullName(): string
    {
        $names = array_filter([$this->firstName, $this->middleName, $this->lastName]);

        $fullName = implode(' ', $names);
        parent::setName($fullName);

        return $fullName;
    }

    public function getRelativeUrl(): string
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getNameOrigin(): NameOrigin
    {
        return $this->nameOrigin;
    }

    public function setNameOrigin(NameOrigin $nameOrigin): void
    {
        $this->nameOrigin = $nameOrigin;
    }

    public function addAffiliation(Affiliation $affiliation): void
    {
        $this->affiliations->add($affiliation);
    }

    public function removeAffiliation(Affiliation $affiliation): void
    {
        $this->affiliations->removeElement($affiliation);
    }

    /**
     * @return ArrayCollection<Affiliation>
     */
    public function getAffiliations(): ArrayCollection
    {
        return $this->affiliations;
    }

    public function hasAffiliations(): bool
    {
        return ! $this->affiliations->isEmpty();
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, ?string $id): self
    {
        $person = new Person(
            $data['firstName'],
            $data['middleName'] ?? null,
            $data['lastName'],
            $data['email'],
            $data['phonenumber'] ?? null,
            isset($data['orcid']) ? new Iri($data['orcid']) : null,
            NameOrigin::peer()
        );

        if ($id !== null) {
            $person->setId($id);
        }

        return $person;
    }
}
