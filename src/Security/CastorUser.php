<?php
/**
 * Created by PhpStorm.
 * User: Martijn
 * Date: 21/06/2018
 * Time: 11:18
 */

namespace App\Security;


use App\Entity\Castor\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class CastorUser
 * @package App\Security
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class CastorUser implements UserInterface, EquatableInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     *
     * @var string|null
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $fullName;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
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
     * @var string|null
     */
    private $nameLast;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string|null
     */
    private $emailAddress;

    /**
     * @var string|null
     */
    private $token;

    const DOMAINS = [
        //'castoredc.com' => ['ROLE_SEMANTIC_EXPERT']
    ];

    const EMAILS = [
        'a.jacobsen@lumc.nl'  => ['ROLE_SEMANTIC_EXPERT'],
        'martijn@castoredc.com' => ['ROLE_SEMANTIC_EXPERT'],
        'demo@castoredc.com' => ['ROLE_SEMANTIC_EXPERT']
    ];

    /**
     * CastorUser constructor.
     * @param null|string $id
     * @param null|string $fullName
     * @param null|string $nameFirst
     * @param null|string $nameMiddle
     * @param null|string $nameLast
     * @param null|string $emailAddress
     * @param string $token
     */
    public function __construct(string $id, string $fullName, ?string $nameFirst, ?string $nameMiddle, ?string $nameLast, string $emailAddress, string $token)
    {
        $this->id = $id;
        $this->fullName = $fullName;
        $this->nameFirst = $nameFirst;
        $this->nameMiddle = $nameMiddle;
        $this->nameLast = $nameLast;
        $this->emailAddress = strtolower($emailAddress);
        $this->token = $token;
        $this->accessTo = new ArrayCollection();
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
     *
     * @param UserInterface $user
     * @return bool
     */
    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof CastorUser) {
            return false;
        }

        if ($this->emailAddress !== $user->getUsername()) {
            return false;
        }

        return true;
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
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $roles = ['ROLE_USER'];
        $domain = substr(strrchr($this->emailAddress, "@"), 1);
        if(isset($this->domains[$domain]))
        {
            $roles = array_merge($roles, $this::DOMAINS[$domain]);
        }
        if(isset($this::EMAILS[$this->emailAddress]))
        {
            $roles = array_merge($roles, $this::EMAILS[$this->emailAddress]);
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
    public function getPassword()
    {
        return null;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->emailAddress;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return null|string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param null|string $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return null|string
     */
    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    /**
     * @param null|string $fullName
     */
    public function setFullName(?string $fullName): void
    {
        $this->fullName = $fullName;
    }

    /**
     * @return null|string
     */
    public function getNameFirst(): ?string
    {
        return $this->nameFirst;
    }

    /**
     * @param null|string $nameFirst
     */
    public function setNameFirst(?string $nameFirst): void
    {
        $this->nameFirst = $nameFirst;
    }

    /**
     * @return null|string
     */
    public function getNameMiddle(): ?string
    {
        return $this->nameMiddle;
    }

    /**
     * @param null|string $nameMiddle
     */
    public function setNameMiddle(?string $nameMiddle): void
    {
        $this->nameMiddle = $nameMiddle;
    }

    /**
     * @return null|string
     */
    public function getNameLast(): ?string
    {
        return $this->nameLast;
    }

    /**
     * @param null|string $nameLast
     */
    public function setNameLast(?string $nameLast): void
    {
        $this->nameLast = $nameLast;
    }

    /**
     * @return null|string
     */
    public function getToken(): ?string
    {
        return $this->token;
    }

    /**
     * @param string|null $token
     */
    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    public static function fromData(User $user, string $token)
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
}