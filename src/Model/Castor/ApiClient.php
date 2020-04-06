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
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Security\ApiUser;
use App\Security\CastorUser;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\MockObject\Api;
use Throwable;
use function json_decode;

class ApiClient
{
    /** @var string */
    private $token = '';

    /** @var Client */
    private $client;

    /** @var ?string */
    private $server;

    /** @var int */
    private $pageSize = 1000;

    public function __construct(string $server = '')
    {
        $this->client = new Client();
        $this->server = $server;
    }

    /**
     * @throws ErrorFetchingCastorData
     */
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
        } catch (Throwable $e) {
            throw new ErrorFetchingCastorData($e->getMessage());
        }

        $data = json_decode((string) $response->getBody(), true);
        $this->token = $data['access_token'];
    }

    /**
     * @return mixed
     *
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
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
            switch ($e->getCode()) {
                case 401:
                    throw new SessionTimedOut();
                case 403:
                    throw new NoAccessPermission();
                case 404:
                    throw new NotFound();
                default:
                    throw new ErrorFetchingCastorData($e->getMessage());
            }
        } catch (Throwable $e) {
            throw new ErrorFetchingCastorData($e->getMessage());
        }

        return $body;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getStudy(string $studyId): Study
    {
        $study = Study::fromData($this->request('/api/study/' . $studyId));
        $study->setFields($this->getFields($study));

        return $study;
    }

    /**
     * @return Study[]
     *
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getStudies(): array
    {
        $return = [];
        $studies = $this->request('/api/study');

        foreach ($studies['_embedded']['study'] as $study) {
            $return[] = Study::fromData($study);
        }

        return $return;
    }

    /**
     * @param Study[]|null $studies
     *
     * @return array<string>
     *
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getStudyIds(?array $studies = null): array
    {
        if ($studies === null) {
            $studies = $this->getStudies();
        }

        $ids = [];

        foreach ($studies as $study) {
            $ids[] = $study->getId();
        }

        return $ids;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
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

    public function setUser(CastorUser $user): void
    {
        $this->token = $user->getToken();
        $this->server = $user->getServer();
    }

    public function useApiUser(ApiUser $user): void
    {
        $this->server = $user->getServer()->getUrl()->getValue();
        $this->auth($user->getClientId(), $user->getClientSecret());
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getFields(Study $study): ArrayCollection
    {
        $pages = 1;
        $fields = new ArrayCollection();

        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getId() . '/field?include=metadata&page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['fields'] as $rawField) {
                $field = Field::fromData($rawField);
                $fields->set($field->getId(), $field);
            }
        }

        return $fields;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getRecord(Study $study, string $recordId): Record
    {
        $body = $this->request('/api/study/' . $study->getId() . '/record/' . $recordId);

        return Record::fromData($body);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getRecords(Study $study, bool $extractArchived = false): ArrayCollection
    {
        $pages = 1;
        $records = new ArrayCollection();

        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getId() . '/record?archived=' . ((int) $extractArchived) . '&page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['records'] as $rawRecord) {
                $record = Record::fromData($rawRecord);
                $records->set($record->getId(), $record);
            }
        }

        return $records;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    private function getRecordStudyData(Study $study, Record $record): RecordData
    {
        $body = $this->request('/api/study/' . $study->getId() . '/record/' . $record->getId() . '/data-point-collection/study');

        return StudyData::fromData($body['_embedded']['items'], $study, $record);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    private function getRecordReportInstances(Study $study, Record $record): ArrayCollection
    {
        $reportInstances = new ArrayCollection();

        try {
            $body = $this->request('/api/study/' . $study->getId() . '/record/' . $record->getId() . '/report-instance');

            foreach ($body['_embedded']['reportInstances'] as $rawReportInstance) {
                $reportInstance = ReportInstance::fromData($rawReportInstance, $record);
                $reportInstances->set($reportInstance->getId(), $reportInstance);
            }
        } catch (NotFound $e) {
            return new ArrayCollection();
        }

        return $reportInstances;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    private function getRecordSurveyPackageInstances(Study $study, Record $record): ArrayCollection
    {
        $surveyPackageInstances = new ArrayCollection();

        $body = $this->request('/api/study/' . $study->getId() . '/surveypackageinstance?record_id=' . $record->getId());

        foreach ($body['_embedded']['surveypackageinstance'] as $rawSurveyPackageInstance) {
            $surveyPackageInstance = SurveyPackageInstance::fromData($rawSurveyPackageInstance, $record);
            $surveyPackageInstances->set($surveyPackageInstance->getId(), $surveyPackageInstance);
        }

        return $surveyPackageInstances;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    private function getRecordSurveyData(Study $study, Record $record): InstanceDataCollection
    {
        $surveyPackageInstances = $this->getRecordSurveyPackageInstances($study, $record);
        $surveyData = new SurveyData($record);

        foreach ($surveyPackageInstances as $surveyPackageInstance) {
            /** @var SurveyPackageInstance $surveyPackageInstance */
            $body = $this->request('/api/study/' . $study->getId() . '/record/' . $record->getId() . '/data-point/survey/' . $surveyPackageInstance->getId());
            $surveyData->addSurveyPackageData($body['_embedded']['SurveyDataPoints'], $study, $surveyPackageInstance);
        }

        return $surveyData;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    private function getRecordReportData(Study $study, Record $record): InstanceDataCollection
    {
        $reportInstances = $this->getRecordReportInstances($study, $record);
        $body = $this->request('/api/study/' . $study->getId() . '/record/' . $record->getId() . '/data-point-collection/report-instance');

        return ReportData::fromData($body['_embedded']['items'], $study, $record, $reportInstances);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
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
