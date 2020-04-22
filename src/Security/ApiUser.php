<?php
declare(strict_types=1);

namespace App\Security;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_api")
 */
class ApiUser
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
    private $emailAddress;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $clientId;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $clientSecret;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorServer")
     * @ORM\JoinColumn(name="server", referencedColumnName="id")
     *
     * @var CastorServer|null
     */
    private $server;

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmailAddress(): string
    {
        return $this->emailAddress;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getServer(): ?CastorServer
    {
        return $this->server;
    }
}
