<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Enum\NameOrigin;
use App\Security\Providers\Castor\CastorUser;
use App\Security\Providers\Orcid\OrcidUser;
use App\Traits\CreatedAt;
use App\Traits\UpdatedAt;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use function array_filter;
use function array_merge;
use function implode;
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
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $nameFirst;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $nameMiddle;
    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $nameLast;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string|null
     */
    private $emailAddress;
    /**
     * @ORM\OneToOne(targetEntity="App\Security\Providers\Castor\CastorUser", cascade={"persist"}, fetch = "EAGER", mappedBy="user")
     * @ORM\JoinColumn(name="castor_user_id", referencedColumnName="id")
     *
     * @var CastorUser|null
     */
    private $castorUser;
    /**
     * @ORM\OneToOne(targetEntity="App\Security\Providers\Orcid\OrcidUser", cascade={"persist"}, fetch = "EAGER", mappedBy="user")
     * @ORM\JoinColumn(name="orcid_user_id", referencedColumnName="orcid")
     *
     * @var OrcidUser|null
     */
    private $orcid;
    /**
     * @ORM\Column(type="NameOriginType")
     *
     * @var NameOrigin
     */
    private $nameOrigin;
    public const DOMAINS = [
        'castoredc.com' => ['ROLE_ADMIN'],
    ];

    public function __construct(string $nameFirst, ?string $nameMiddle, string $nameLast, NameOrigin $nameOrigin, ?string $emailAddress)
    {
        $this->nameFirst = $nameFirst;
        $this->nameMiddle = $nameMiddle;
        $this->nameLast = $nameLast;
        $this->nameOrigin = $nameOrigin;
        $this->emailAddress = $emailAddress !== null ? strtolower($emailAddress) : null;
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
        if ($this->emailAddress !== null) {
            return $this->emailAddress;
        }

        if ($this->hasOrcid()) {
            return $this->orcid->getOrcid();
        }

        if ($this->hasCastorUser()) {
            return $this->castorUser->getEmailAddress();
        }

        return '';
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getFullName(): string
    {
        $names = array_filter([$this->nameFirst, $this->nameMiddle, $this->nameLast]);

        return implode(' ', $names);
    }

    public function getNameFirst(): string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(string $nameFirst): void
    {
        $this->nameFirst = $nameFirst;
    }

    public function getNameMiddle(): ?string
    {
        return $this->nameMiddle;
    }

    public function setNameMiddle(?string $nameMiddle): void
    {
        $this->nameMiddle = $nameMiddle;
    }

    public function getNameLast(): string
    {
        return $this->nameLast;
    }

    public function setNameLast(string $nameLast): void
    {
        $this->nameLast = $nameLast;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress ?? '';
    }

    public function setEmailAddress(string $emailAddress): void
    {
        $this->emailAddress = $emailAddress;
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

    public function getNameOrigin(): NameOrigin
    {
        return $this->nameOrigin;
    }

    public function setNameOrigin(NameOrigin $nameOrigin): void
    {
        $this->nameOrigin = $nameOrigin;
    }
}
