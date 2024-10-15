<?php
declare(strict_types=1);

namespace App\Service\Distribution;

use App\Entity\Connection\DistributionDatabaseInformation;
use App\Graph\SparqlResponse;
use App\Service\EncryptionService;

interface DistributionService
{
    public function runQuery(string $query, DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): SparqlResponse;

    public function createDatabase(DistributionDatabaseInformation $databaseInformation): void;

    public function createUsers(DistributionDatabaseInformation $databaseInformation, EncryptionService $encryptionService): void;

    /** @param array<mixed>|null $nameSpaces */
    public function getDataFromStore(
        DistributionDatabaseInformation $databaseInformation,
        EncryptionService $encryptionService,
        ?string $namedGraphUrl = null,
        ?array $nameSpaces = null,
    ): mixed;
}
