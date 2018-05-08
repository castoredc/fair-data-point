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

    private $pageSize = 1000;

    /**
     * ApiClient constructor.
     * @param string $server
     */
    public function __construct(
        $server = 'https://data.castoredc.com'
    )
    {
        $this->client = new Client();
        $this->server = $server;
    }

    public function auth(string $clientId, string $secret)
    {
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

    public function getStudy(string $studyId)
    {
        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $studyId,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json'
                ]
            ]
        );
        $study = json_decode($response->getBody(), true);
        return $study;
    }

    public function getMetadata(string $studyId)
    {
        $pages = 1;
        $metadatas = [];
        for($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $studyId . '/metadata?page=' . $page . '&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json'
                    ]
                ]
            );
            $body = json_decode($response->getBody(), true);
            $pages = $body['page_count'];

            foreach($body['_embedded']['metadatas'] as $metadata)
            {
                $metadatas[$metadata['element_id']][$metadata['description']][$metadata['metadata_type']['name']] = $metadata['value'];
            }
        }
        return $metadatas;
    }

    public function getFields(string $studyId)
    {
        $pages = 1;
        $fields = [];
        for($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $studyId . '/field?page=' . $page . '&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json'
                    ]
                ]
            );
            $body = json_decode($response->getBody(), true);
            $pages = $body['page_count'];

            $fields = array_merge($fields, $body['_embedded']['fields']);
        }
        return $fields;
    }

    public function getRecords(string $studyId)
    {
        $pages = 1;
        $records = [];
        for($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $studyId . '/record?page='. $page .'&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json'
                    ]
                ]
            );
            $body = json_decode($response->getBody(), true);
            $pages = $body['page_count'];

            $records = array_merge($records, $body['_embedded']['records']);
        }
        return $records;
    }

    public function getRecordDataPoints(string $studyId, string $recordId)
    {
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
        $body = json_decode($response->getBody(), true);
        $dataPoints = $body['_embedded']['items'];

        return $dataPoints;
    }

}