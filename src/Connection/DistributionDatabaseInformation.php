<?php
declare(strict_types=1);

namespace App\Connection;

use App\Entity\FAIRData\Distribution;
use Doctrine\ORM\Mapping as ORM;
use Hackzilla\PasswordGenerator\Generator\ComputerPasswordGenerator;
use Hackzilla\PasswordGenerator\RandomGenerator\Php7RandomGenerator;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution_databases")
 */
class DistributionDatabaseInformation
{
    public const DBNAME_PREPEND = 'dist_';
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
     * @ORM\Column(type="string", name="user")
     *
     * @var string
     */
    private $username;

    /**
     * @ORM\Column(type="string", name="pass")
     *
     * @var string
     */
    private $password;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
        $this->database = $this::DBNAME_PREPEND . $distribution->getId();

        $generator = new ComputerPasswordGenerator();
        $generator->setRandomGenerator(new Php7RandomGenerator());
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_LENGTH, 13);

        $this->username = $this::USERNAME_PREPEND . $generator->generatePassword();

        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_SYMBOLS, true);
        $generator->setOptionValue(ComputerPasswordGenerator::OPTION_LENGTH, 32);

        $this->password = $generator->generatePassword();
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
