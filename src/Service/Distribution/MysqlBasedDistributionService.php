<?php
declare(strict_types=1);

namespace App\Service\Distribution;

use App\Entity\Connection\DistributionDatabaseInformation;
use App\Exception\CouldNotConnectToMySqlServer;
use App\Exception\CouldNotCreateDatabase;
use App\Exception\CouldNotCreateDatabaseUser;
use App\Graph\SparqlResponse;
use App\Service\EncryptionService;
use ARC2_Store;
use ARC2_StoreEndpoint;
use Doctrine\DBAL\Configuration as DBALConfiguration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use Doctrine\DBAL\Schema\MySQLSchemaManager;
use EasyRdf\Graph;
use EasyRdf\Utils;
use Throwable;
use function boolval;
use function explode;
use function header_remove;
use function headers_list;
use function is_array;
use function sprintf;
use function trim;

class MysqlBasedDistributionService implements DistributionService
{
    public const CURRENT_STORE = 'current';
    public const PREVIOUS_STORE = 'previous';

    private string $host;

    private string $user;

    private string $pass;

    private int $port;

    private bool $useSsl;

    private string $certificate;

    public function __construct(string $host = '', string $user = '', string $pass = '', int $port = 3306, bool $useSsl = false, string $certificate = '')
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
        $this->useSsl = $useSsl;
        $this->certificate = $certificate;
    }

    /** @return mixed[] */
    private function getOptions(): array
    {
        $options = [];

        if ($this->useSsl) {
            $options[1009] = $this->certificate;
        }

        return $options;
    }

    /** @throws CouldNotConnectToMySqlServer */
    public function createCreatorConnection(): Connection
    {
        $config = new DBALConfiguration();

        $params = [
            'driver' => 'pdo_mysql',
            'host' => $this->host,
            'user' => $this->user,
            'password' => $this->pass,
            'port' => $this->port,
            'driverOptions' => $this->getOptions(),
        ];

        try {
            return DriverManager::getConnection($params, $config);
        } catch (Throwable $t) {
            throw new CouldNotConnectToMySqlServer();
        }
    }

    /** @throws Exception */
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
            'driverOptions' => $this->getOptions(),
        ];

        return DriverManager::getConnection($params, $config);
    }

    /** @throws Exception */
    public function getArc2Store(string $store, DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService, bool $setupStore = true): ARC2_Store
    {
        $params = [
            'db_adapter' => 'doctrine',
            'connection' => $this->createDistributionConnection($databaseInformation, $encryptionService),
            'store_name' => $store,
            'store_engine_type' => 'InnoDB',
        ];

        $store = new ARC2_Store($params, $this);

        $store->createDBCon();

        if ($setupStore && ! $store->isSetUp()) {
            $store->setUp();
        }

        return $store;
    }

    /** @throws Exception */
    public function getArc2Endpoint(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): ARC2_StoreEndpoint
    {
        $params = [
            'db_adapter' => 'doctrine',
            'connection' => $this->createDistributionConnection($databaseInformation, $encryptionService),

            'store_name' => self::CURRENT_STORE,
            'store_engine_type' => 'InnoDB',

            'endpoint_features' => [ 'select', 'construct', 'ask', 'describe' ],
        ];

        $endpoint = new ARC2_StoreEndpoint($params, $this);

        if (! $endpoint->isSetUp()) {
            $endpoint->setUp(); /* create MySQL tables */
        }

        return $endpoint;
    }

    /** @throws Exception */
    public function runQuery(string $query, DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): SparqlResponse
    {
        $endpoint = $this->getArc2Endpoint($databaseInformation, $encryptionService);
        $_GET['format'] = 'SPARQLJSON';

        $endpoint->handleQueryRequest($query);
        $contents = $endpoint->getResult();
        $endpoint->sendHeaders();

        $headers = $this->parseHeaders();
        [$contentType] = Utils::parseMimeType($headers['Content-Type']);

        return new SparqlResponse(
            $contents,
            $contentType,
            'arc2/' . $databaseInformation->getDatabase()
        );
    }

    /** @return string[] */
    private function parseHeaders(): array
    {
        $parsed = [];

        $headers = headers_list();
        header_remove();

        foreach ($headers as $header) {
            // split each header by ':' and assign them to $key and $value
            [$key, $value] = explode(':', $header, 2); // limit the explode to 2 items.
            // add trimed variables to the new array
            $parsed[trim($key)] = trim($value);
        }

        return $parsed;
    }

    public function getDataFromStore(
        DistributionDatabaseInformation $databaseInformation,
        EncryptionService $encryptionService,
        ?string $namedGraphUrl = null,
        ?array $nameSpaces = null
    ): mixed {
        $store = $this->getArc2Store(
            self::CURRENT_STORE,
            $databaseInformation,
            $encryptionService
        );

        if ($namedGraphUrl !== null) {
            $result = $store->query(sprintf('SELECT ?s ?p ?o WHERE { GRAPH ?g { ?s ?p ?o . FILTER (?g = <%s>)} }', $namedGraphUrl));
        } else {
            $result = $store->query('SELECT * WHERE { ?s ?p ?o . }');
        }

        return is_array($result) ? $store->toTurtle($result['result']['rows'], $nameSpaces) : 0;
    }

    public function addDataToStore(
        DistributionDatabaseInformation $databaseInformation,
        EncryptionService $encryptionService,
        Graph $graph,
        string $graphUrl
    ): void {
        $store = $this->getArc2Store(
            self::CURRENT_STORE,
            $databaseInformation,
            $encryptionService
        );

        $store->delete(false, $graphUrl);
        $store->insert($graph->serialise('turtle'), $graphUrl);
    }

    public function optimizeStore(
        DistributionDatabaseInformation $databaseInformation,
        EncryptionService $encryptionService
    ): void {
        $store = $this->getArc2Store(
            self::CURRENT_STORE,
            $databaseInformation,
            $encryptionService
        );

//        $store->optimizeTables();
    }

    /** @throws CouldNotConnectToMySqlServer */
    public function createDatabase(DistributionDatabaseInformation $databaseInformation): void
    {
        $manager = new MySQLSchemaManager($this->createCreatorConnection(), new MySQL80Platform());
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
    public function createUsers(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): void
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
            throw new CouldNotCreateDatabaseUser($t->getMessage());
        }

        try {
            $connection->executeStatement(
                'GRANT SELECT, INSERT, UPDATE, DELETE, TRIGGER, EXECUTE, LOCK TABLES, CREATE, DROP, CREATE TEMPORARY TABLES
                ON `' . $databaseInformation->getEscapedDatabase() . '`.* TO \'' . $databaseInformation->getDecryptedUsername($encryptionService)->exposeAsString() . '\'@\'%\';'
            );
        } catch (Throwable $t) {
            throw new CouldNotCreateDatabase($t->getMessage());
        }
    }
}
