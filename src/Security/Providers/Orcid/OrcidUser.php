<?php
declare(strict_types=1);

namespace App\Security\Providers\Orcid;

use App\Security\Providers\ProviderUser;
use App\Security\User;
use App\Traits\CreatedAt;
use App\Traits\UpdatedAt;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="orcid_user")
 * @ORM\HasLifecycleCallbacks
 */
class OrcidUser implements ProviderUser
{
    use CreatedAt;
    use UpdatedAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Security\User", cascade={"persist"}, fetch = "EAGER", inversedBy="orcid")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private ?User $user = null;
    /**
     * @ORM\Id
     * @ORM\Column(type="string")
     */
    private string $orcid;
    /** @ORM\Column(type="string", length=255) */
    private string $name;
    private ?string $token = null;

    public function __construct(string $orcid, string $name, ?string $token)
    {
        $this->orcid = $orcid;
        $this->name = $name;
        $this->token = $token;
    }

    public function getOrcid(): string
    {
        return $this->orcid;
    }

    public function setOrcid(string $orcid): void
    {
        $this->orcid = $orcid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): void
    {
        $this->token = $token;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'orcid' => $this->orcid,
            'name' => $this->name,
        ];
    }

    /** @inheritDoc */
    public function getId()
    {
        return $this->orcid;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }
}
