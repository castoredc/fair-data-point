<?php
declare(strict_types=1);

namespace App\Model\Stardog;

use App\Entity\Encryption\SensitiveDataString;
use App\Exception\ErrorFetchingStardogData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Throwable;
use function array_keys;
use function array_map;
use function implode;
use function json_decode;
use function json_encode;
use function sprintf;
use function str_split;
use function urlencode;

class ApiClient
{
    private const METHOD_POST = 'POST';
    private const METHOD_PUT = 'PUT';
    private const METHOD_GET = 'GET';

    private Client $client;
    private string $host;
    private string $user;
    private string $pass;
    private int $port;
    private ?string $database;

    public function __construct(string $host, string $user, string $pass, int $port)
    {
        $this->client = new Client();

        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
    }

    public function getDatabase(): ?string
    {
        return $this->database;
    }

    public function setDatabase(?string $database): void
    {
        $this->database = $database;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return mixed
     *
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function jsonRequest(string $uri, string $method, array $data, bool $multipart = false)
    {
        return json_decode((string) $this->handleRequest($method, $uri, [
            'auth' => [
                $this->user,
                $this->pass,
            ],
            'json' => $data,
        ]), true);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return mixed
     *
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function multipartRequest(string $uri, string $method, array $data)
    {
        return json_decode((string) $this->handleRequest($method, $uri, [
            'auth' => [
                $this->user,
                $this->pass,
            ],
            'multipart' => array_map(static function (string $key, string $value): array {
                return [
                    'name' => $key,
                    'contents' => $value,
                ];
            }, array_keys($data), $data),
        ]), true);
    }

    /**
     * @return mixed
     *
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function graphRequest(string $uri, string $method, ?string $data)
    {
        return (string) $this->handleRequest($method, $uri, [
            'auth' => [
                $this->user,
                $this->pass,
            ],
            'body' => $data,
            'headers' => ['Content-Type' => 'text/turtle'],
        ]);
    }

    public function addUser(SensitiveDataString $username, SensitiveDataString $password): void
    {
        $this->jsonRequest(
            '/admin/users',
            self::METHOD_POST,
            [
                'username' => $username->exposeAsString(),
                'password' => str_split($password->exposeAsString()),
            ]
        );
    }

    public function createDatabase(string $name): void
    {
        $this->multipartRequest(
            '/admin/databases',
            self::METHOD_POST,
            [
                'root' => json_encode(['dbname' => $name]),
            ]
        );
    }

    public function addRole(string $name): void
    {
        $this->jsonRequest(
            '/admin/roles',
            self::METHOD_POST,
            ['rolename' => $name]
        );
    }

    public function addRolePermissionForDatabase(string $role, string $action, string $database): void
    {
        $this->jsonRequest(
            '/admin/permissions/role/' . $role,
            self::METHOD_PUT,
            [
                'action' => $action,
                'resource_type' => '*',
                'resource' => [$database],
            ]
        );
    }

    public function addRoleToUser(SensitiveDataString $username, string $role): void
    {
        $this->jsonRequest(
            '/admin/users/' . $username->exposeAsString() . '/roles',
            self::METHOD_POST,
            ['rolename' => $role]
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
     * @param array<string, mixed> $options
     *
     * @return mixed
     *
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function handleRequest(string $method, string $uri, array $options)
    {
        try {
            $response = $this->client->request(
                $method,
                $this->host . ':' . $this->port . $uri,
                $options,
            );

            return $response->getBody();
        } catch (RequestException $e) {
            switch ($e->getCode()) {
                case 401:
                    throw new SessionTimedOut();

                case 403:
                    throw new NoAccessPermission($e->getMessage(), $e->getCode(), $e);

                case 404:
                    throw new NotFound($e->getMessage(), $e->getCode(), $e);

                default:
                    throw new ErrorFetchingStardogData($e->getMessage(), $e->getCode(), $e);
            }
        } catch (Throwable $e) {
            throw new ErrorFetchingStardogData($e->getMessage());
        }
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
