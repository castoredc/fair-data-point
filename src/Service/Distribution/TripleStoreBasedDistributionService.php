<?php
declare(strict_types=1);

namespace App\Service\Distribution;

use App\Entity\Connection\DistributionDatabaseInformation;
use App\Exception\CouldNotCreateDatabase;
use App\Exception\CouldNotCreateDatabaseUser;
use App\Graph\SparqlClient;
use App\Graph\SparqlResponse;
use App\Model\Stardog\AdminApiClient;
use App\Model\Stardog\DatabaseApiClient;
use App\Service\EncryptionService;
use EasyRdf\Graph;
use Throwable;

class TripleStoreBasedDistributionService implements DistributionService
{
    private DatabaseApiClient $client;

    public function __construct(private string $host = '', private string $protocol = '', private string $user = '', private string $pass = '', private int $port = 8081)
    {
    }

    private function createCreatorClient(): AdminApiClient
    {
        return new AdminApiClient(
            $this->getUrl(),
            $this->user,
            $this->pass,
            $this->port
        );
    }

    public function createDatabaseApiClient(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): void
    {
        $this->client = new DatabaseApiClient(
            $databaseInformation->getDatabase(),
            $this->host,
            $databaseInformation->getDecryptedUsername($encryptionService)->exposeAsString(),
            $databaseInformation->getDecryptedPassword($encryptionService)->exposeAsString(),
            $this->port
        );
    }

    public function createReadOnlyDatabaseApiClient(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): void
    {
        $this->client = new DatabaseApiClient(
            $databaseInformation->getDatabase(),
            $this->host,
            $databaseInformation->getDecryptedReadOnlyUsername($encryptionService)->exposeAsString(),
            $databaseInformation->getDecryptedReadOnlyPassword($encryptionService)->exposeAsString(),
            $this->port
        );
    }

    public function addDataToStore(Graph $graph, string $graphUrl): void
    {
        $this->client->addDataToNamedGraph(
            $graph->serialise('turtle'),
            $graphUrl
        );
    }

    /** @throws CouldNotCreateDatabase */
    public function createDatabase(DistributionDatabaseInformation $databaseInformation): void
    {
        $client = $this->createCreatorClient();

        try {
            $client->createDatabase($databaseInformation->getDatabase());

            $client->addRole($databaseInformation->getRole());
            $client->addRolePermissionForDatabase($databaseInformation->getRole(), 'all', $databaseInformation->getDatabase());

            $client->addRole($databaseInformation->getReadOnlyRole());
            $client->addRolePermissionForDatabase($databaseInformation->getReadOnlyRole(), 'read', $databaseInformation->getDatabase());
        } catch (Throwable $t) {
            throw new CouldNotCreateDatabase($t->getMessage());
        }
    }

    /** @throws CouldNotCreateDatabaseUser */
    public function createUsers(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): void
    {
        $client = $this->createCreatorClient();

        try {
            $client->addUser(
                $databaseInformation->getDecryptedUsername($encryptionService),
                $databaseInformation->getDecryptedPassword($encryptionService)
            );
            $client->addRoleToUser(
                $databaseInformation->getDecryptedUsername($encryptionService),
                $databaseInformation->getRole()
            );

            $client->addUser(
                $databaseInformation->getDecryptedReadOnlyUsername($encryptionService),
                $databaseInformation->getDecryptedReadOnlyPassword($encryptionService)
            );
            $client->addRoleToUser(
                $databaseInformation->getDecryptedReadOnlyUsername($encryptionService),
                $databaseInformation->getReadOnlyRole()
            );
        } catch (Throwable $t) {
            throw new CouldNotCreateDatabaseUser($t->getMessage());
        }
    }

    /** @param array<mixed>|null $nameSpaces */
    public function getDataFromStore(
        DistributionDatabaseInformation $databaseInformation,
        EncryptionService $encryptionService,
        ?string $namedGraphUrl = null,
        ?array $nameSpaces = null,
    ): mixed {
        $this->createReadOnlyDatabaseApiClient($databaseInformation, $encryptionService);

        return $this->client->getDataFromStore($namedGraphUrl);
    }

    /** @param array<string, string> $namespaces */
    public function importNamespaces(array $namespaces): void
    {
        $this->client->importNamespaces($namespaces);
    }

    private function getUrl(): string
    {
        return $this->protocol . '://' . $this->host;
    }

    public function runQuery(string $query, DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): SparqlResponse
    {
        $sparqlClient = new SparqlClient(
            $this->getUrl() . ':' . $this->port . '/' . $databaseInformation->getDatabase() . '/query?graph-uri=tag:stardog:api:context:all',
            $databaseInformation->getDecryptedReadOnlyUsername($encryptionService),
            $databaseInformation->getDecryptedReadOnlyPassword($encryptionService),
        );

        return $sparqlClient->query($query);
    }
}
