<?php
declare(strict_types=1);

namespace App\Security;

use App\Entity\Encryption\EncryptedString;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\Iri;
use App\Exception\CouldNotDecrypt;
use App\Exception\CouldNotTransformEncryptedStringToJson;
use App\Service\EncryptionService;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use function filter_var;
use const FILTER_VALIDATE_URL;

/** @ORM\Entity(repositoryClass="App\Repository\CastorServerRepository") */
class CastorServer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private int $id;

    /** @ORM\Column(type="iri") */
    private Iri $url;

    /** @ORM\Column(type="string", length=255) */
    private string $name;

    /** @ORM\Column(type="string", length=255) */
    private string $flag;

    /** @ORM\Column(type="boolean") */
    private bool $default;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private string $clientId;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private string $clientSecret;

    /** @throws InvalidArgumentException */
    private function __construct(Iri $uri, string $name, string $flag, bool $default = false)
    {
        if (filter_var($uri->getValue(), FILTER_VALIDATE_URL) === false) {
            throw new InvalidArgumentException('Invalid Castor EDC server URI provided.');
        }

        $this->url = $uri;
        $this->name = $name;
        $this->flag = $flag;
        $this->default = $default;
    }

    /** @throws InvalidArgumentException */
    public static function nonDefaultServer(string $uri, string $name, string $flag): CastorServer
    {
        return new self(new Iri($uri), $name, $flag);
    }

    /** @throws InvalidArgumentException */
    public static function defaultServer(string $uri, string $name, string $flag): CastorServer
    {
        return new self(new Iri($uri), $name, $flag, true);
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public static function withClientCredentials(
        CastorServer $castorServer,
        EncryptionService $encryptionService,
        string $clientId,
        string $clientSecret
    ): CastorServer {
        $encryptedClientId = json_encode($encryptionService->encrypt(new SensitiveDataString($clientId)));

        if ($encryptedClientId === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $encryptedClientSecret = json_encode($encryptionService->encrypt(new SensitiveDataString($clientSecret)));

        if ($encryptedClientSecret === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $castorServer->clientId = $encryptedClientId;
        $castorServer->clientSecret = $encryptedClientSecret;

        return $castorServer;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUrl(): Iri
    {
        return $this->url;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFlag(): string
    {
        return $this->flag;
    }

    public function isDefault(): bool
    {
        return $this->default;
    }

    /** @throws CouldNotDecrypt */
    public function getDecryptedClientId(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->clientId));
    }

    /** @throws CouldNotDecrypt */
    public function getDecryptedClientSecret(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->clientSecret));
    }

    public function getClientIdCiphertext(): string
    {
        return $this->clientId;
    }

    public function getClientSecretCiphertext(): string
    {
        return $this->clientSecret;
    }
}
