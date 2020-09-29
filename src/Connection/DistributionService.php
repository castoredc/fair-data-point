<?php
declare(strict_types=1);

namespace App\Connection;

use App\Encryption\EncryptionService;
use App\Exception\CouldNotConnectToMySqlServer;
use App\Exception\CouldNotCreateDatabase;
use App\Exception\CouldNotCreateDatabaseUser;
use ARC2;
use ARC2_Store;
use ARC2_StoreEndpoint;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\MySqlSchemaManager;
use Throwable;
use function boolval;
use function sprintf;

class DistributionService
{
    public const CURRENT_STORE = 'current';
    public const PREVIOUS_STORE = 'previous';

    private string $host;

    private string $user;

    private string $pass;

    private int $port;

    public function __construct(string $host = '', string $user = '', string $pass = '', int $port = 3306)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
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
            'port' => $this->port,
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
    public function createDistributionConnection(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): Connection
    {
        $config = new DBALConfiguration();

        $params = [
            'driver' => 'pdo_mysql',
            'host' => $this->host,
            'user' => $databaseInformation->getDecryptedUsername($encryptionService)->exposeAsString(),
            'password' => $databaseInformation->getDecryptedPassword($encryptionService)->exposeAsString(),
            'dbname' => $databaseInformation->getDatabase(),
            'port' => $this->port,
        ];

        return DriverManager::getConnection($params, $config);
    }

    /**
     * @throws DBALException
     */
    public function getArc2Store(string $store, DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService, bool $setupStore = true): ARC2_Store
    {
        $params = [
            'db_adapter' => 'pdo',
            'db_pdo_protocol' => 'mysql',
            'db_host' => $this->host . ':' . $this->port,
            'db_user' => $databaseInformation->getDecryptedUsername($encryptionService)->exposeAsString(),
            'db_pwd' => $databaseInformation->getDecryptedPassword($encryptionService)->exposeAsString(),
            'db_name' => $databaseInformation->getDatabase(),
            'store_name' => $store,
        ];

        $store = ARC2::getStore($params);

        $store->createDBCon();

        if ($setupStore && ! $store->isSetUp()) {
            $store->setUp();
        }

        return $store;
    }

    /**
     * @throws DBALException
     */
    public function getArc2Endpoint(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): ARC2_StoreEndpoint
    {
        $params = [
            'db_adapter' => 'pdo',
            'db_pdo_protocol' => 'mysql',
            'db_host' => $this->host . ':' . $this->port,
            'db_user' => $databaseInformation->getDecryptedUsername($encryptionService)->exposeAsString(),
            'db_pwd' => $databaseInformation->getDecryptedPassword($encryptionService)->exposeAsString(),
            'db_name' => $databaseInformation->getDatabase(),

            'store_name' => self::CURRENT_STORE,

            'endpoint_features' => [ 'select', 'construct', 'ask', 'describe' ],
        ];

        $endpoint = ARC2::getStoreEndpoint($params);

        if (! $endpoint->isSetUp()) {
            $endpoint->setUp(); /* create MySQL tables */
        }

        return $endpoint;
    }

    /**
     * @throws CouldNotConnectToMySqlServer
     */
    public function createDatabase(DistributionDatabaseInformation $databaseInformation): void
    {
        $manager = new MySqlSchemaManager($this->createCreatorConnection());
        $manager->createDatabase('`' . $databaseInformation->getDatabase() . '`');
    }

    public function duplicateArc2Store(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): ARC2_Store
    {
        $previousStore = $this->getArc2Store(self::PREVIOUS_STORE, $databaseInformation, $encryptionService, false);
        $hasPreviousStore = boolval($previousStore->isSetUp());

        $currentStore = $this->getArc2Store(self::CURRENT_STORE, $databaseInformation, $encryptionService, false);
        $hasCurrentStore = boolval($currentStore->isSetUp());

        if ($hasPreviousStore) {
            $previousStore->drop();
        }

        if ($hasCurrentStore) {
            $currentStore->replicateTo(self::PREVIOUS_STORE);
        } else {
            $currentStore->setUp();
        }

        return $currentStore;
    }

    /**
     * @throws CouldNotCreateDatabaseUser
     * @throws CouldNotCreateDatabase
     * @throws CouldNotConnectToMySqlServer
     */
    public function createMysqlUser(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): void
    {
        $connection = $this->createCreatorConnection();

        try {
            $sql = sprintf(
                "CREATE USER '%s'@'%%' IDENTIFIED BY '%s'",
                $databaseInformation->getDecryptedUsername($encryptionService)->exposeAsString(),
                $databaseInformation->getDecryptedPassword($encryptionService)->exposeAsString()
            );
            $connection->executeStatement($sql);
        } catch (Throwable $t) {
            throw new CouldNotCreateDatabaseUser();
        }

        try {
            $connection->executeStatement(
                'GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER, EXECUTE, LOCK TABLES, CREATE, DROP, CREATE TEMPORARY TABLES
                ON `' . $databaseInformation->getEscapedDatabase() . '`.* TO \'' . $databaseInformation->getDecryptedUsername($encryptionService)->exposeAsString() . '\'@\'%\';'
            );
        } catch (Throwable $t) {
            throw new CouldNotCreateDatabase();
        }
    }
}
