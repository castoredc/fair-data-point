<?php
declare(strict_types=1);

namespace App\Connection;

use App\Exception\CouldNotConnectToMySqlServer;
use App\Exception\CouldNotCreateDatabase;
use App\Exception\CouldNotCreateDatabaseUser;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Throwable;
use function sprintf;

class DistributionService
{
    /** @var Connection */
    protected $connection;

    /** @var string */
    private $host;

    /** @var string */
    private $user;

    /** @var string */
    private $pass;

    public function __construct(Connection $connection, string $host = '', string $user = '', string $pass = '')
    {
        $this->connection = $connection;
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
    }

    /**
     * @throws CouldNotConnectToMySqlServer
     */
    public function createCreatorConnection(): Connection
    {
        $config = new DBALConfiguration();

        $params = [
            'driver' => 'pdo_mysql',
            'host' => $this->host,
            'user' => $this->user,
            'password' => $this->pass,
            'dbname' => null,
        ];

        try {
            return DriverManager::getConnection($params, $config);
        } catch (Throwable $t) {
            throw new CouldNotConnectToMySqlServer();
        }
    }

    /**
     * @throws DBALException
     */
    public function createDistributionConnection(DistributionDatabaseInformation $databaseInformation): Connection
    {
        $config = new DBALConfiguration();

        $params = [
            'driver' => 'pdo_mysql',
            'host' => $this->host,
            'user' => $databaseInformation->getUsername(),
            'password' => $databaseInformation->getPassword(),
            'dbname' => $databaseInformation->getDatabase(),
        ];

        return DriverManager::getConnection($params, $config);
    }

    /**
     * @throws CouldNotConnectToMySqlServer
     */
    public function createDatabase(DistributionDatabaseInformation $databaseInformation): void
    {
        $manager = new MySqlSchemaManager($this->createCreatorConnection());
        $manager->createDatabase('`' . $databaseInformation->getDatabase() . '`');
    }

    /**
     * @throws CouldNotCreateDatabaseUser
     * @throws CouldNotCreateDatabase
     * @throws CouldNotConnectToMySqlServer
     */
    public function createMysqlUser(DistributionDatabaseInformation $databaseInformation): void
    {
        $connection = $this->createCreatorConnection();

        try {
            $sql = sprintf("CREATE USER '%s'@'%%' IDENTIFIED BY '%s'", $databaseInformation->getUsername(), $databaseInformation->getPassword());
            $connection->exec($sql);
        } catch (Throwable $t) {
            throw new CouldNotCreateDatabaseUser();
        }

        try {
            $connection->exec(
                'GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER, EXECUTE, LOCK TABLES
                ON `' . $databaseInformation->getEscapedDatabase() . '`.* TO \'' . $databaseInformation->getUsername() . '\'@\'%\';'
            );
        } catch (Throwable $t) {
            throw new CouldNotCreateDatabase();
        }
    }
}
