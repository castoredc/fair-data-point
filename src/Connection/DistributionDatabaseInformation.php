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
    public const DBNAME_PREPEND = 'dist_';
    public const DBNAME_PREPEND_ESCAPE = 'dist\_';
    public const USERNAME_PREPEND = 'du_';

    /**
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Distribution", inversedBy="databaseInformation")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Distribution
     */
    private $distribution;

    /**
     * @ORM\Column(type="string", name="db")
     *
     * @var string
     */
    private $database;

    /**
     * @ORM\Column(type="string", name="user", type="text", length=65535, nullable=false)
     *
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", name="pass", type="text", length=65535, nullable=false)
     *
     * @var string
     */
    private $password;

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

    public function getDecryptedUsername(EncryptionService $encryptionService): string
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->username))->exposeAsString();
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getDecryptedPassword(EncryptionService $encryptionService): string
    {
        return $encryptionService->decrypt(EncryptedString::fromJsonString($this->password))->exposeAsString();
    }

    /**
     * @throws CouldNotTransformEncryptedStringToJson
     */
    public function setUsername(EncryptionService $cryptobox, string $username): void
    {
        $encoded = json_encode($cryptobox->encrypt(new SensitiveDataString($username)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->username = $encoded;
    }

    /**
     * @throws CouldNotTransformEncryptedStringToJson
     */
    public function setPassword(EncryptionService $cryptobox, string $password): void
    {
        $encoded = json_encode($cryptobox->encrypt(new SensitiveDataString($password)));

        if ($encoded === false) {
            throw new CouldNotTransformEncryptedStringToJson();
        }

        $this->password = $encoded;
    }
}
