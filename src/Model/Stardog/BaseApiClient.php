<?php
declare(strict_types=1);

namespace App\Model\Stardog;

use App\Exception\ErrorFetchingStardogData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Throwable;
use function array_keys;
use function array_map;
use function json_decode;

abstract class BaseApiClient
{
    protected const METHOD_POST = 'POST';
    protected const METHOD_PUT = 'PUT';
    protected const METHOD_GET = 'GET';

    private Client $client;

    public function __construct(private string $host, private string $user, private string $pass, private int $port)
    {
        $this->client = new Client();
    }

    /**
     * @param array<string, mixed> $data
     *
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    protected function jsonRequest(string $uri, string $method, array $data): mixed
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
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    protected function multipartRequest(string $uri, string $method, array $data): mixed
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
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    protected function graphRequest(string $uri, string $method, ?string $data): mixed
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

    /**
     * @param array<string, mixed> $options
     *
     * @throws ErrorFetchingStardogData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    private function handleRequest(string $method, string $uri, array $options): mixed
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
}
