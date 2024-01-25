<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\DataSpecification\Common\DataSpecificationPermission;
use App\Entity\Enum\Wizard;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\Permission\CatalogPermission;
use App\Entity\FAIRData\Permission\DatasetPermission;
use App\Entity\FAIRData\Permission\DistributionPermission;
use App\Security\Providers\Castor\CastorUser;
use App\Security\Providers\Orcid\OrcidUser;
use App\Traits\CreatedAt;
use App\Traits\UpdatedAt;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function array_merge;
use function in_array;
use function strrchr;
use function strtolower;
use function substr;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
    use CreatedAt;
    use UpdatedAt;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string $id;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Agent\Person", cascade={"persist"}, fetch = "EAGER", mappedBy="user")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     */
    private ?Person $person = null;
    /**
     * @ORM\OneToOne(targetEntity="App\Security\Providers\Castor\CastorUser", cascade={"persist"}, fetch = "EAGER", mappedBy="user")
     * @ORM\JoinColumn(name="castor_user_id", referencedColumnName="id")
     */
    private ?CastorUser $castorUser = null;
    /**
     * @ORM\OneToOne(targetEntity="App\Security\Providers\Orcid\OrcidUser", cascade={"persist"}, fetch = "EAGER", mappedBy="user")
     * @ORM\JoinColumn(name="orcid_user_id", referencedColumnName="orcid")
     */
    private ?OrcidUser $orcid = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DataSpecification\Common\DataSpecificationPermission", mappedBy="user", cascade={"persist", "remove"})
     *
     * @var Collection<DataSpecificationPermission>
     */
    private Collection $dataSpecifications;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Permission\CatalogPermission", mappedBy="user", cascade={"persist", "remove"})
     *
     * @var Collection<CatalogPermission>
     */
    private Collection $catalogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Permission\DatasetPermission", mappedBy="user", cascade={"persist", "remove"})
     *
     * @var Collection<DatasetPermission>
     */
    private Collection $datasets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Permission\DistributionPermission", mappedBy="user", cascade={"persist", "remove"})
     *
     * @var Collection<DistributionPermission>
     */
    private Collection $distributions;

    public const DOMAINS = [
        'castoredc.com' => ['ROLE_ADMIN'],
    ];

    public const EMAILS = [];

    public function __construct(?Person $person)
    {
        $this->person = $person;
        $this->dataSpecifications = new ArrayCollection();
        $this->catalogs = new ArrayCollection();
        $this->datasets = new ArrayCollection();
        $this->distributions = new ArrayCollection();
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return $this->id === $user->getId() && $this->updatedAt === $user->getUpdatedAt();
    }

    /** @return array<string> The user roles */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->hasCastorUser()) {
            $roles[] = 'ROLE_CASTOR_USER';

            $email = strtolower($this->castorUser->getEmailAddress());
            $domain = strrchr($email, '@');

            if ($domain !== false) {
                $domain = substr($domain, 1);
            }

            if (isset($this::DOMAINS[$domain])) {
                $roles = array_merge($roles, $this::DOMAINS[$domain]);
            }

            if (isset($this::EMAILS[$email])) {
                $roles = array_merge($roles, $this::EMAILS[$email]);
            }
        }

        if ($this->hasOrcid()) {
            $roles[] = 'ROLE_ORCID_USER';
        }

        return $roles;
    }

    public function isAdmin(): bool
    {
        return in_array('ROLE_ADMIN', $this->getRoles(), true);
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        if ($this->hasOrcid()) {
            return $this->orcid->getOrcid();
        }

        if ($this->hasCastorUser()) {
            return $this->castorUser->getEmailAddress();
        }

        return '';
    }

    public function getEmailAddress(): ?string
    {
        if ($this->getPerson() !== null) {
            return $this->person->getEmail();
        }

        if ($this->hasCastorUser()) {
            return $this->castorUser->getEmailAddress();
        }

        return null;
    }

    /** @return ArrayCollection<Wizard> */
    public function getWizards(): ArrayCollection
    {
        $wizards = new ArrayCollection();

        if ($this->getEmailAddress() === null) {
            $wizards->add(Wizard::email());
        }

        if ($this->getPerson() === null) {
            $wizards->add(Wizard::details());
        } elseif (! $this->getPerson()->hasAffiliations()) {
            $wizards->add(Wizard::affiliations());
        }

        return $wizards;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function hasCastorUser(): bool
    {
        return $this->castorUser !== null;
    }

    public function getCastorUser(): ?CastorUser
    {
        return $this->castorUser;
    }

    public function setCastorUser(?CastorUser $castorUser): void
    {
        $this->castorUser = $castorUser;
    }

    public function getOrcid(): ?OrcidUser
    {
        return $this->orcid;
    }

    public function hasOrcid(): bool
    {
        return $this->orcid !== null;
    }

    public function setOrcid(?OrcidUser $orcid): void
    {
        $this->orcid = $orcid;
    }

    public function getPerson(): ?Person
    {
        return $this->person;
    }

    public function setPerson(?Person $person): void
    {
        $this->person = $person;
    }

    /** @return Collection<DataSpecificationPermission> */
    public function getDataSpecifications(): Collection
    {
        return $this->dataSpecifications;
    }

    /** @return Collection<CatalogPermission> */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    /** @return Collection<DatasetPermission> */
    public function getDatasets(): Collection
    {
        return $this->datasets;
    }

    /** @return Collection<DistributionPermission> */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }

    public function getUserIdentifier(): string
    {
        return $this->getId();
    }
}
