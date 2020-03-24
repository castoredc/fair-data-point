<?php
declare(strict_types=1);

namespace App\Model\Castor;

use App\Entity\Castor\Data\ReportData;
use App\Entity\Castor\Data\StudyData;
use App\Entity\Castor\Data\SurveyData;
use App\Entity\Castor\Field;
use App\Entity\Castor\InstanceDataCollection;
use App\Entity\Castor\Instances\ReportInstance;
use App\Entity\Castor\Instances\SurveyPackageInstance;
use App\Entity\Castor\Record;
use App\Entity\Castor\RecordData;
use App\Entity\Castor\RecordDataCollection;
use App\Entity\Castor\Study;
use App\Entity\Castor\User;
use App\Exception\NoPermissionException;
use App\Exception\SessionTimeOutException;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
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
                throw new SessionTimeOutException();
            }

            if ($e->getCode() === 403) {
                throw new NoPermissionException();
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
        $study = Study::fromData($this->request('/api/study/' . $studyId));
        $study->setFields($this->getFields($study));

        return $study;
    }

    /**
     * @returns Study[]
     * @throws Exception
     */
    public function getStudies(): array
    {
        $return = [];
        $studies = $this->request('/api/study');

        foreach($studies['_embedded']['study'] as $study) {
            $return[] = Study::fromData($study);
        }

        return $return;
    }

    /**
     * @throws Exception
     */
    public function getUser(): User
    {
        $body = $this->request('/api/user');

        return User::fromData($body['_embedded']['user'][0]);
    }

    /**
     * @param mixed $token
     */
    public function setToken($token): void
    {
        $this->token = $token;
    }

    public function getFields(Study $study): ArrayCollection
    {
        $pages = 1;
        $fields = new ArrayCollection();

        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $study->getId() . '/field?include=metadata&page=' . $page . '&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );
            $body = json_decode((string) $response->getBody(), true);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['fields'] as $rawField) {
                $field = Field::fromData($rawField);
                $fields->set($field->getId(), $field);
            }
        }

        return $fields;
    }

    public function getRecord(Study $study, string $recordId): Record
    {
        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $study->getId() . '/record/' . $recordId,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        $body = json_decode((string) $response->getBody(), true);

        return Record::fromData($body);
    }

    public function getRecords(Study $study, bool $extractArchived = false): ArrayCollection
    {
        $pages = 1;
        $records = new ArrayCollection();

        for ($page = 1; $page <= $pages; $page++) {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $study->getId() . '/record?archived=' . ((int) $extractArchived) . '&page=' . $page . '&page_size=' . $this->pageSize,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );

            $body = json_decode((string) $response->getBody(), true);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['records'] as $rawRecord) {
                $record = Record::fromData($rawRecord);
                $records->set($record->getId(), $record);
            }
        }

        return $records;
    }

    private function getRecordStudyData(Study $study, Record $record): RecordData
    {
        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $study->getId() . '/record/' . $record->getId() . '/data-point-collection/study',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        $body = json_decode((string) $response->getBody(), true);

        return StudyData::fromData($body['_embedded']['items'], $study, $record);
    }

    /**
     * @throws Exception
     */
    private function getRecordReportInstances(Study $study, Record $record): ArrayCollection
    {
        $reportInstances = new ArrayCollection();

        try {
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $study->getId() . '/record/' . $record->getId() . '/report-instance',
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );

            $body = json_decode((string) $response->getBody(), true);

            foreach ($body['_embedded']['reportInstances'] as $rawReportInstance) {
                $reportInstance = ReportInstance::fromData($rawReportInstance, $record);
                $reportInstances->set($reportInstance->getId(), $reportInstance);
            }
        } catch (GuzzleException $e) {
            if ($e->getCode() === 404) {
                return new ArrayCollection();
            }
        }

        return $reportInstances;
    }

    /**
     * @throws Exception
     */
    private function getRecordSurveyPackageInstances(Study $study, Record $record): ArrayCollection
    {
        $surveyPackageInstances = new ArrayCollection();

        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $study->getId() . '/surveypackageinstance?record_id=' . $record->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        $body = json_decode((string) $response->getBody(), true);

        foreach ($body['_embedded']['surveypackageinstance'] as $rawSurveyPackageInstance) {
            $surveyPackageInstance = SurveyPackageInstance::fromData($rawSurveyPackageInstance, $record);
            $surveyPackageInstances->set($surveyPackageInstance->getId(), $surveyPackageInstance);
        }

        return $surveyPackageInstances;
    }

    private function getRecordSurveyData(Study $study, Record $record): InstanceDataCollection
    {
        $surveyPackageInstances = $this->getRecordSurveyPackageInstances($study, $record);

        $surveyData = new SurveyData($record);

        foreach ($surveyPackageInstances as $surveyPackageInstance) {
            /** @var SurveyPackageInstance $surveyPackageInstance */
            $response = $this->client->request(
                'GET',
                $this->server . '/api/study/' . $study->getId() . '/record/' . $record->getId() . '/data-point/survey/' . $surveyPackageInstance->getId(),
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json',
                    ],
                ]
            );

            $body = json_decode((string) $response->getBody(), true);

            $surveyData->addSurveyPackageData($body['_embedded']['SurveyDataPoints'], $study, $surveyPackageInstance);
        }

        return $surveyData;
    }

    private function getRecordReportData(Study $study, Record $record): InstanceDataCollection
    {
        $reportInstances = $this->getRecordReportInstances($study, $record);

        $response = $this->client->request(
            'GET',
            $this->server . '/api/study/' . $study->getId() . '/record/' . $record->getId() . '/data-point-collection/report-instance',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token,
                    'Accept' => 'application/json',
                ],
            ]
        );

        $body = json_decode((string) $response->getBody(), true);

        return ReportData::fromData($body['_embedded']['items'], $study, $record, $reportInstances);
    }

    public function getRecordDataCollection(Study $study, Record $record): Record
    {
        $dataCollection = new RecordDataCollection($record);

        $dataCollection->setStudyData($this->getRecordStudyData($study, $record));
        $dataCollection->setReportData($this->getRecordReportData($study, $record));
        $dataCollection->setSurveyData($this->getRecordSurveyData($study, $record));

        $record->setData($dataCollection);

        return $record;
    }
}
