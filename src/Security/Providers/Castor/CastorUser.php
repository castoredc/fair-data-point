<?php
declare(strict_types=1);

namespace App\Security\Providers\Castor;

use App\Security\User;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use function in_array;
use function strtolower;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CastorUserRepository")
 * @ORM\Table(name="castor_user")
 */
class CastorUser implements ResourceOwnerInterface
{
    /**
     * @ORM\OneToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="castorUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var User|null
     */
    private $user;
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
    /** @var string|null */
    private $server;
    /** @var string[] */
    private $studies;

    public function __construct(string $id, string $nameFirst, ?string $nameMiddle, string $nameLast, string $emailAddress)
    {
        $this->id = $id;
        $this->nameFirst = $nameFirst;
        $this->nameMiddle = $nameMiddle;
        $this->nameLast = $nameLast;
        $this->emailAddress = strtolower($emailAddress);
        $this->studies = [];
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
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

    public function getServer(): ?string
    {
        return $this->server;
    }

    public function setServer(?string $server): void
    {
        $this->server = $server;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    /**
     * @return string[]
     */
    public function getStudies(): array
    {
        return $this->studies;
    }

    /**
     * @param string[] $studies
     */
    public function setStudies(array $studies): void
    {
        $this->studies = $studies;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function hasAccessToStudy(string $studyId): bool
    {
        return in_array($studyId, $this->studies, true);
    }

    /**
     * Return all of the owner details available as an array.
     *
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nameFirst' => $this->nameFirst,
            'nameMiddle' => $this->nameMiddle,
            'nameLast' => $this->nameLast,
            'emailAddress' => $this->emailAddress,
        ];
    }
}
