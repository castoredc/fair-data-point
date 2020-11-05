<?php
declare(strict_types=1);

namespace App\Model\Grid;

use App\Entity\Grid\Institute;
use App\Exception\ErrorFetchingGridData;
use App\Exception\NotFound;
use App\Factory\Grid\InstituteFactory;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Throwable;
use function http_build_query;
use function json_decode;

class ApiClient
{
    private Client $client;

    private ApiEndpoint $endpoint;

    private InstituteFactory $instituteFactory;

    public function __construct(ApiEndpoint $endpoint, InstituteFactory $instituteFactory)
    {
        $this->client = new Client();
        $this->endpoint = $endpoint;
        $this->instituteFactory = $instituteFactory;
    }

    /**
     * @return mixed
     *
     * @throws NotFound
     * @throws ErrorFetchingGridData
     */
    private function request(string $uri)
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->endpoint->getUrl() . $uri,
                [
                    'headers' => ['Accept' => 'application/json'],
                ]
            );

            $body = json_decode((string) $response->getBody(), true);
        } catch (RequestException $e) {
            switch ($e->getCode()) {
                case 404:
                    throw new NotFound($e->getMessage(), $e->getCode(), $e);

                default:
                    throw new ErrorFetchingGridData($e->getMessage(), $e->getCode(), $e);
            }
        } catch (Throwable $e) {
            throw new ErrorFetchingGridData($e->getMessage());
        }

        return $body;
    }

    /**
     * @throws NotFound
     * @throws ErrorFetchingGridData
     */
    public function getInstituteById(string $id): Institute
    {
        $data = $this->request('/api/institute/id/' . $id);

        return $this->instituteFactory->createFromGridApiData($data);
    }

    /**
     * @return Institute[]
     *
     * @throws NotFound
     * @throws ErrorFetchingGridData
     */
    public function findInstitutesByDomain(string $domain): array
    {
        $data = $this->request('/api/institute/domain?' . http_build_query(['domain' => $domain]));

        return $this->parseInstitutes($data);
    }

    /**
     * @return Institute[]
     *
     * @throws NotFound
     * @throws ErrorFetchingGridData
     */
    public function findInstitutesByNameAndCountry(string $name, ?string $country): array
    {
        $data = $this->request('/api/institute/name?' . http_build_query([
            'name' => $name,
            'country' => $country,
        ]));

        return $this->parseInstitutes($data);
    }

    /**
     * @param array<mixed> $data
     *
     * @return Institute[]
     */
    private function parseInstitutes(array $data): array
    {
        $institutes = [];

        foreach ($data as $institute) {
            $institutes[] = $this->instituteFactory->createFromGridApiData($institute);
        }

        return $institutes;
    }
}
