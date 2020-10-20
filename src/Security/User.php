<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\FAIRData\Agent\Person;
use App\Security\Providers\Castor\CastorUser;
use App\Security\Providers\Orcid\OrcidUser;
use App\Traits\CreatedAt;
use App\Traits\UpdatedAt;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use function array_merge;
use function preg_replace;
use function strpos;
use function strrchr;
use function substr;
use function trim;

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
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;
    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Person", cascade={"persist"}, fetch = "EAGER", mappedBy="user")
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
    public const DOMAINS = [
        'castoredc.com' => ['ROLE_ADMIN'],
    ];

    public function __construct(?Person $person)
    {
        $this->person = $person;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof User) {
            return false;
        }

        return $this->id === $user->getId() && $this->updatedAt === $user->getUpdatedAt();
    }

    /**
     * @return array<string> The user roles
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];

        if ($this->hasCastorUser()) {
            $roles[] = 'ROLE_CASTOR_USER';

            $domain = strrchr($this->castorUser->getEmailAddress(), '@');

            if ($domain !== false) {
                $domain = substr($domain, 1);
            }

            if (isset($this::DOMAINS[$domain])) {
                $roles = array_merge($roles, $this::DOMAINS[$domain]);
            }
        }

        if ($this->hasOrcid()) {
            $roles[] = 'ROLE_ORCID_USER';
        }

        return $roles;
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

    public function shouldShowDetailsSuggestions(): bool
    {
        return $this->getEmailAddress() === null;
    }

    /** @return mixed[] */
    public function getDetailsSuggestions(): array
    {
        $firstName = '';
        $lastName = '';

        if ($this->hasOrcid()) {
            $name = trim($this->orcid->getName());
            $lastName = strpos($name, ' ') === false ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $firstName = trim(preg_replace('#' . $lastName . '#', '', $name));
        }

        return [$firstName, $lastName];
    }

    /** @return mixed[] */
    public function getWizards(): array
    {
        $wizards = [];

        if ($this->getEmailAddress() === null) {
            $wizards['email'] = true;
        }

        if ($this->getPerson() === null) {
            $wizards['details'] = true;
        }

//        elseif ($this->getPerson()-> !== null) {
//            $wizards['details'] = true;
//        }

        return $wizards;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getId(): string
    {
        return $this->id;
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
}
