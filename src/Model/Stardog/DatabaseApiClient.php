<?php
declare(strict_types=1);

namespace App\Model\Stardog;

use App\Exception\ErrorFetchingStardogData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use function array_keys;
use function array_map;
use function implode;
use function sprintf;
use function urlencode;

class DatabaseApiClient extends BaseApiClient
{
    protected ?string $database;

    public function __construct(?string $database, string $host, string $user, string $pass, int $port)
    {
        parent::__construct($host, $user, $pass, $port);

        $this->database = $database;
    }

    /** @return mixed */
    public function getDataFromStore(?string $namedGraphUrl)
    {
        return $this->graphRequest(
            '/' . $this->database . ($namedGraphUrl !== null ? '/?graph=' . urlencode($namedGraphUrl) : '/?graph=stardog:context:all'),
            self::METHOD_GET,
            null
        );
    }

    public function addDataToNamedGraph(string $turtle, string $graphUrl): void
    {
        $this->graphRequest(
            '/' . $this->database . '/?graph=' . urlencode($graphUrl),
            self::METHOD_PUT,
            $turtle
        );
    }

    /**
     * @param array<string, string> $namespaces
     *
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function importNamespaces(array $namespaces): void
    {
        $this->graphRequest(
            '/' . $this->database . '/namespaces',
            self::METHOD_POST,
            implode("\n", array_map(static function (string $prefix, string $uri): string {
                return sprintf('@prefix %s: <%s> . ', $prefix, $uri);
            }, array_keys($namespaces), $namespaces))
        );
    }
}
