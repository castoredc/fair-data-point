<?php
declare(strict_types=1);

namespace App\Entity\Connection;

use App\Entity\Encryption\EncryptedString;
use App\Entity\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;
use App\Exception\CouldNotTransformEncryptedStringToJson;
use App\Service\EncryptionService;
use Doctrine\ORM\Mapping as ORM;
use function json_encode;
use function str_replace;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_databases")
 */
class DistributionDatabaseInformation
{
    public const DBNAME_PREPEND = 'fdp_dist_';
    public const DBNAME_PREPEND_ESCAPE = 'fdp\_dist\_';
    public const USERNAME_PREPEND = 'fdp_u_';
    public const READ_ONLY_USERNAME_PREPEND = 'fdp_u-ro_';
    public const ROLE_PREPEND = 'fdp_r_';
    public const READ_ONLY_ROLE_PREPEND = 'fdp_r-ro_';

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Distribution", inversedBy="databaseInformation")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     */
    private Distribution $distribution;

    /** @ORM\Column(type="string", name="database_name") */
    private string $database;

    /** @ORM\Column(type="string", name="user", type="text", length=65535, nullable=false) */
    private string $username;

    /** @ORM\Column(type="string", name="password", type="text", length=65535, nullable=false) */
    private string $password;

    /** @ORM\Column(type="string", name="readonly_user", type="text", length=65535, nullable=false) */
    private string $readOnlyUsername;

    /** @ORM\Column(type="string", name="readonly_password", type="text", length=65535, nullable=false) */
    private string $readOnlyPassword;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
        $this->database = $this::DBNAME_PREPEND . $distribution->getId();
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getRole(): string
    {
        return str_replace(self::DBNAME_PREPEND, self::ROLE_PREPEND, $this->database);
    }

    public function getReadOnlyRole(): string
    {
        return str_replace(self::DBNAME_PREPEND, self::READ_ONLY_ROLE_PREPEND, $this->database);
    }

    public function getEscapedDatabase(): string
    {
        return str_replace($this::DBNAME_PREPEND, $this::DBNAME_PREPEND_ESCAPE, $this->database);
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getDecryptedUsername(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->username));
    }

    public function getReadOnlyUsername(): string
    {
        return $this->readOnlyUsername;
    }

    public function getDecryptedReadOnlyUsername(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->readOnlyUsername));
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDecryptedPassword(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->password));
    }

    public function getReadOnlyPassword(): string
    {
        return $this->readOnlyPassword;
    }

    public function getDecryptedReadOnlyPassword(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->readOnlyPassword));
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public function setUsername(EncryptionService $encryptionService, string $username): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($username)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->username = $encoded;
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public function setPassword(EncryptionService $encryptionService, string $password): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($password)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->password = $encoded;
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public function setReadOnlyUsername(EncryptionService $encryptionService, string $readOnlyUsername): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($readOnlyUsername)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->readOnlyUsername = $encoded;
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public function setReadOnlyPassword(EncryptionService $encryptionService, string $readOnlyPassword): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($readOnlyPassword)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->readOnlyPassword = $encoded;
    }
}
