<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Castor\User;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use function array_merge;
use function strrchr;
use function strtolower;
use function substr;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class CastorUser implements UserInterface, ResourceOwnerInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $fullName;

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
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $emailAddress;

    /** @var string|null */
    private $token;

    public const DOMAINS = [
        'castoredc.com' => ['ROLE_ADMIN'],
    ];

    public function __construct(string $id, string $fullName, ?string $nameFirst, ?string $nameMiddle, ?string $nameLast, string $emailAddress, string $token)
    {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->nameFirst = $nameFirst;
        $this->nameMiddle = $nameMiddle;
        $this->nameLast = $nameLast;
        $this->emailAddress = strtolower($emailAddress);
        $this->token = $token;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * Also implementation should consider that $user instance may implement
     * the extended user interface `AdvancedUserInterface`.
     */
    public function isEqualTo(UserInterface $user): bool
    {
        if (! $user instanceof CastorUser) {
            return false;
        }

        return $this->emailAddress === $user->getUsername();
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array<string> The user roles
     */
    public function getRoles(): array
    {
        $roles = ['ROLE_USER'];
        $domain = strrchr($this->emailAddress, '@');

        if ($domain !== false) {
            $domain = substr($domain, 1);
        }
        if (isset($this::DOMAINS[$domain])) {
            $roles = array_merge($roles, $this::DOMAINS[$domain]);
        }

        return $roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword(): string
    {
        return '';
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->emailAddress;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    public function setNameFirst(?string $nameFirst): void
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

    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    public function setNameLast(?string $nameLast): void
    {
        $this->nameLast = $nameLast;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public static function fromData(User $user, string $token): CastorUser
    {
        return new CastorUser(
            $user->getId(),
            $user->getFullName(),
            $user->getNameFirst(),
            $user->getNameMiddle(),
            $user->getNameLast(),
            $user->getEmailAddress(),
            $token
        );
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'fullName' => $this->fullName,
            'nameFirst' => $this->nameFirst,
            'nameMiddle' => $this->nameMiddle ,
            'nameLast' => $this->nameLast,
            'emailAddress' => $this->emailAddress,
        ];
    }
}
