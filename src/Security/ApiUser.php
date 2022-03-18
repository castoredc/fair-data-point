<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Encryption\EncryptedString;
use App\Entity\Encryption\SensitiveDataString;
use App\Exception\CouldNotTransformEncryptedStringToJson;
use App\Service\EncryptionService;
use Doctrine\ORM\Mapping as ORM;
use function json_encode;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_api")
 */
class ApiUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string", length=255) */
    private string $emailAddress;

    /** @ORM\Column(type="string", type="text", length=65535, nullable=false) */
    private string $clientId;

    /** @ORM\Column(type="string", type="text", length=65535, nullable=false) */
    private string $clientSecret;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorServer")
     * @ORM\JoinColumn(name="server", referencedColumnName="id")
     */
    private ?CastorServer $server = null;

    public function __construct(string $emailAddress, ?CastorServer $server)
    {
        $this->emailAddress = $emailAddress;
        $this->server = $server;
    }

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

    public function getDecryptedClientId(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->clientId));
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public function setDecryptedClientId(EncryptionService $encryptionService, string $clientId): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($clientId)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->clientId = $encoded;
    }

    public function getClientSecret(): string
    {
        return $this->clientSecret;
    }

    public function getDecryptedClientSecret(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->clientSecret));
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public function setDecryptedClientSecret(EncryptionService $encryptionService, string $clientSecret): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($clientSecret)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->clientSecret = $encoded;
    }

    public function getServer(): ?CastorServer
    {
        return $this->server;
    }
}
