<?php
declare(strict_types=1);

namespace App\Model\Castor;

use App\Entity\Castor\Study;
use App\Entity\Castor\User;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use function array_merge;
use function json_decode;

class ApiClient
{
    /** @var string */
    private $token = '';

    /** @var Client */
    private $client;

    /** @var string */
    private $server = '';

    /** @var int */
    private $pageSize = 1000;

    public function __construct(string $castorEdcUrl)
    {
        $this->client = new Client();
        $this->server = $castorEdcUrl;
    }

    public function auth(string $clientId, string $secret): void
    {
        try {
            $response = $this->client->request(
                'POST',
                $this->server . '/oauth/token',
                [
                    'json' => [
                        'client_id' => $clientId,
                        'client_secret' => $secret,
                        'grant_type' => 'client_credentials',
                    ],
                ]
            );
        } catch (GuzzleException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $data = json_decode((string) $response->getBody(), true);
        $this->token = $data['access_token'];
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    private function request(string $uri)
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->server . $uri,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );

            $body = json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            if ($e->getCode() === 401) {
                throw new UnauthorizedHttpException('', $e->getMessage());
            }

            if ($e->getCode() === 403) {
                throw new UnauthorizedHttpException('', 'You do not have permission to access this');
            }

            throw new HttpException(500, $e->getMessage());
        } catch (GuzzleException $e) {
            throw new HttpException(500, $e->getMessage());
        }

        return $body;
    }

    /**
     * @throws Exception
     */
    public function getStudy(string $studyId): Study
    {
        $study = $this->request('/api/study/' . $studyId);

        return Study::fromData($study);
    }

    /**
     * @return mixed
     *
     * @throws Exception
     */
    public function getRecordDataPoints(string $studyId, string $recordId)
    {
        $body = $this->request('/api/study/' . $studyId . '/record/' . $recordId . '/data-point-collection/study');

        return $body['_embedded']['items'];
    }

    /**
     * @throws Exception
     */
    public function getUser(): User
    {
        $body = $this->request('/api/user');
        $user = $body['_embedded']['user'][0];

        return User::fromData($user);
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    /**
     * @return array<array>
     */
    public function getRawMetadata(string $studyId): array
    {
        $pages = 1;
        $metadatas = [];
        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $studyId . '/metadata?page=' . $page . '&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $body = json_decode((string) $response->getBody(), true);
            $pages = $body['page_count'];
            foreach ($body['_embedded']['metadatas'] as $metadata) {
                $metadatas[$metadata['element_id']][$metadata['description']][$metadata['metadata_type']['name']] = $metadata['value'];
            }
        }

        return $metadatas;
    }

    /**
     * @return array<array>
     */
    public function getRawFields(string $studyId): array
    {
        $pages = 1;
        $fields = [];
        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $studyId . '/field?page=' . $page . '&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $body = json_decode((string) $response->getBody(), true);
            $pages = $body['page_count'];
            $fields = array_merge($fields, $body['_embedded']['fields']);
        }

        return $fields;
    }

    /**
     * @return array<array>
     */
    public function getRawRecords(string $studyId): array
    {
        $pages = 1;
        $records = [];
        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $studyId . '/record?page=' . $page . '&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $body = json_decode((string) $response->getBody(), true);
            $pages = $body['page_count'];
            $records = array_merge($records, $body['_embedded']['records']);
        }
        $return = [];
        foreach ($records as $record) {
            $return[$record['record_id']] = $record;
        }

        return $return;
    }

    /**
     * @return mixed
     */
    public function getRawRecordDataPoints(string $studyId, string $recordId)
    {
        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $studyId . '/record/' . $recordId . '/data-point-collection/study',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ],
            ]
        );
        $body = json_decode((string) $response->getBody(), true);

        return $body['_embedded']['items'];
    }
}
