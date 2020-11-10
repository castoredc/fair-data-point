<?php
declare(strict_types=1);

namespace App\Connection;

use App\Encryption\EncryptedString;
use App\Encryption\EncryptionService;
use App\Encryption\SensitiveDataString;
use App\Entity\FAIRData\Distribution;
use App\Exception\CouldNotTransformEncryptedStringToJson;
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

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDecryptedPassword(EncryptionService $encryptionService): SensitiveDataString
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->password));
    }

    /**
     * @throws CouldNotTransformEncryptedStringToJson
     */
    public function setUsername(EncryptionService $encryptionService, string $username): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($username)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->username = $encoded;
    }

    /**
     * @throws CouldNotTransformEncryptedStringToJson
     */
    public function setPassword(EncryptionService $encryptionService, string $password): void
    {
        $encoded = json_encode($encryptionService->encrypt(new SensitiveDataString($password)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->password = $encoded;
    }
}
