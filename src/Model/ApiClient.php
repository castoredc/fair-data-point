<?php
namespace App\Model;


use GuzzleHttp\Client;

class ApiClient
{
    /**
     * Token
     */
    private $token = '';

    /** @var Client */
    private $client;

    /**
     * @var string
     */
    private $server = '';

    /**
     * @var bool
     */
    private $useCache = false;

    /**
     * ApiClient constructor.
     * @param bool $useCache
     * @param string $server
     */
    public function __construct(
        $useCache = false,
        $server = 'https://data.castoredc.com'
    )
    {
        $this->client = new Client();
        $this->server = $server;
        $this->useCache = $useCache;
    }

    public function auth(string $clientId, string $secret)
    {
        if ($this->useCache) {
            return;
        }
        $response = $this->client->request('POST',
            $this->server . '/oauth/token',
            [
                'json' => [
                    'client_id'     => $clientId,
                    'client_secret' => $secret,
                    'grant_type'    => 'client_credentials'
                ]
            ]
        );

        $data = json_decode($response->getBody(), true);
        $this->token = $data['access_token'];
    }

    public function getRecords(string $studyId)
    {
        if ($this->useCache) {
            $records = (json_decode(file_get_contents(__DIR__ . '/records.json'), true));
            return $records['_embedded']['records'];

        }
        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $studyId . '/record',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json'
                ]
            ]
        );
        $records = json_decode($response->getBody(), true);
        return $records['_embedded']['records'];
    }

    public function getRecordDataPoints(string $studyId, string $recordId)
    {
        if ($this->useCache) {
            $data = (json_decode(file_get_contents(__DIR__ . '/record-data.json'), true));
            return $data['_embedded']['items'];

        }
        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $studyId . '/record/' . $recordId . '/data-point-collection/study',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json'
                ]
            ]
        );
        $data = json_decode($response->getBody(), true);
        return $data['_embedded']['items'];
    }

}