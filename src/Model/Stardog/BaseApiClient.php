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
    private string $host;
    private string $user;
    private string $pass;
    private int $port;

    public function __construct(string $host, string $user, string $pass, int $port)
    {
        $this->client = new Client();

        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
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
    protected function jsonRequest(string $uri, string $method, array $data)
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
    protected function multipartRequest(string $uri, string $method, array $data)
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
    protected function graphRequest(string $uri, string $method, ?string $data)
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
}
