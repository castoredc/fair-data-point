<?php
declare(strict_types=1);

namespace App\Model\Castor;

use App\Encryption\EncryptedString;
use App\Encryption\EncryptionService;
use App\Encryption\SensitiveDataString;
use App\Entity\Castor\CastorStudy;
use App\Entity\Castor\Data\InstanceDataCollection;
use App\Entity\Castor\Data\RecordData;
use App\Entity\Castor\Data\RecordDataCollection;
use App\Entity\Castor\Data\ReportData;
use App\Entity\Castor\Data\StudyData;
use App\Entity\Castor\Data\SurveyData;
use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Form\FieldOptionGroup;
use App\Entity\Castor\Instances\ReportInstance;
use App\Entity\Castor\Instances\SurveyPackageInstance;
use App\Entity\Castor\Record;
use App\Entity\Castor\Structure\Phase;
use App\Entity\Castor\Structure\Report;
use App\Entity\Castor\Structure\Step\ReportStep;
use App\Entity\Castor\Structure\Step\StudyStep;
use App\Entity\Castor\Structure\StructureCollection\PhaseCollection;
use App\Entity\Castor\Structure\StructureCollection\ReportCollection;
use App\Entity\Castor\Structure\StructureCollection\StructureCollection;
use App\Entity\Castor\Structure\StructureCollection\SurveyCollection;
use App\Entity\Castor\Structure\Survey;
use App\Entity\Castor\User;
use App\Exception\ErrorFetchingCastorData;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Exception\SessionTimedOut;
use App\Security\ApiUser;
use App\Security\CastorUser;
use ArrayIterator;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Throwable;
use function iterator_to_array;
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

    public function useApiUser(ApiUser $user, EncryptionService $encryptionService): void
    {
        $this->server = $user->getServer()->getUrl()->getValue();
        $this->auth(
            $encryptionService->decrypt(EncryptedString::fromJsonString($user->getClientId())),
            $encryptionService->decrypt(EncryptedString::fromJsonString($user->getClientSecret()))
        );
    }

    /**
     * @throws ErrorFetchingCastorData
     */
    public function auth(SensitiveDataString $clientId, SensitiveDataString $secret): void
    {
        try {
            $response = $this->client->request(
                'POST',
                $this->server . '/oauth/token',
                [
                    'json' => [
                        'client_id' => $clientId->exposeAsString(),
                        'client_secret' => $secret->exposeAsString(),
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
                    throw new NoAccessPermission($e->getMessage(), $e->getCode(), $e);
                case 404:
                    throw new NotFound($e->getMessage(), $e->getCode(), $e);
                default:
                    throw new ErrorFetchingCastorData($e->getMessage(), $e->getCode(), $e);
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
    public function getStudy(string $studyId): CastorStudy
    {
        $study = CastorStudy::fromData($this->request('/api/study/' . $studyId));
        $study->setFields($this->getFields($study));

        return $study;
    }

    /**
     * @return CastorStudy[]
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
            $return[] = CastorStudy::fromData($study);
        }

        return $return;
    }

    /**
     * @param CastorStudy[]|null $studies
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
            $ids[] = $study->getSourceId();
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
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getFields(CastorStudy $study): ArrayCollection
    {
        $pages = 1;
        $fields = new ArrayCollection();

        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/field?include=metadata|optiongroup&page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['fields'] as $rawField) {
                $field = Field::fromData($rawField, $study);
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
    public function getField(CastorStudy $study, string $fieldId): Field
    {
        $body = $this->request('/api/study/' . $study->getSourceId() . '/field/' . $fieldId . '?include=metadata');

        return Field::fromData($body, $study);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getFieldByParent(CastorStudy $study, string $parentId): ArrayCollection
    {
        $fields = $this->getFields($study);
        $results = new ArrayCollection();

        foreach ($fields->getIterator() as $field) {
            if ($field->getParentId() !== $parentId) {
                continue;
            }

            $results->set($field->getId(), $field);
        }

        /** @var ArrayIterator $iterator */
        $iterator = $results->getIterator();

        $iterator->uasort(static function (Field $a, Field $b) {
            if ($a->getNumber() === $b->getNumber()) {
                return 0;
            }

            return $a->getNumber() < $b->getNumber() ? -1 : 1;
        });

        return new ArrayCollection(iterator_to_array($iterator));
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getPhasesAndSteps(CastorStudy $study, bool $includeFields = false): PhaseCollection
    {
        $phases = $this->getPhases($study);
        $steps = $this->getStudySteps($study, $includeFields);

        foreach ($steps->getIterator() as $step) {
            $phase = $phases->get($step->getParentId());
            $step->setParent($phase);
            $phase->addStep($step);
        }

        $phases->order();

        return $phases;
    }

    /**
     * @throws Exception
     */
    public function getPhases(CastorStudy $study): PhaseCollection
    {
        $pages = 1;
        $phases = new PhaseCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/phase?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['phases'] as $phase) {
                $phases->add(Phase::fromData($phase, $study));
            }
        }

        return $phases;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getStudySteps(CastorStudy $study, bool $includeFields = false): ArrayCollection
    {
        $pages = 1;
        $steps = new ArrayCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/step?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['steps'] as $step) {
                $newStep = StudyStep::fromData($step, $study);

                if ($includeFields) {
                    $fields = $this->getFieldByParent($study, $step['step_id']);
                    $newStep->setFields($fields->toArray());
                }

                $steps->set($step['step_id'], $newStep);
            }
        }

        return $steps;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getSurveys(CastorStudy $study, bool $includeFields = false): SurveyCollection
    {
        $pages = 1;
        $surveys = new SurveyCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/survey?include=steps&page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['surveys'] as $survey) {
                $tempSurvey = Survey::fromData($survey, $study);

                if ($includeFields) {
                    $steps = [];
                    foreach ($tempSurvey->getSteps() as $surveyStep) {
                        $fields = $this->getFieldByParent($study, $surveyStep->getId());
                        $surveyStep->setFields($fields->toArray());
                        $steps[] = $surveyStep;
                    }
                    $tempSurvey->setSteps($steps);
                }

                $tempSurvey->setStepParent();
                $surveys->add($tempSurvey);
            }
        }

        return $surveys;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getSurvey(CastorStudy $study, string $surveyId): Survey
    {
        $body = $this->request('/api/study/' . $study->getSourceId() . '/survey/' . $surveyId);

        return Survey::fromData($body, $study);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getReports(CastorStudy $study, bool $includeFields = false): ReportCollection
    {
        $pages = 1;
        $reports = new ReportCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/report?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['reports'] as $report) {
                $tempReport = Report::fromData($report, $study);
                $steps = $this->request('/api/study/' . $study->getSourceId() . '/report/' . $report['report_id'] . '/report-step?page=' . $page . '&page_size=' . $this->pageSize);

                foreach ($steps['_embedded']['report_steps'] as $step) {
                    $newStep = ReportStep::fromData($step, $study);

                    if ($includeFields) {
                        $fields = $this->getFieldByParent($study, $step['id']);
                        $newStep->setFields($fields->toArray());
                    }

                    $tempReport->addStep($newStep);
                }
                $tempReport->setStepParent();
                $reports->add($tempReport);
            }
        }

        return $reports;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getReport(CastorStudy $study, string $reportId): Report
    {
        $body = $this->request('/api/study/' . $study->getSourceId() . '/report/' . $reportId);

        return Report::fromData($body, $study);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getOptionGroups(CastorStudy $study): CastorEntityCollection
    {
        /** @var CastorEntityCollection<FieldOptionGroup> $optionGroups */
        $optionGroups = new CastorEntityCollection();

        $pages = 1;

        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/field-optiongroup?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['fieldOptionGroups'] as $optionGroup) {
                $optionGroups->add(FieldOptionGroup::fromData($optionGroup, $study));
            }
        }

        return $optionGroups;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getOptionGroup(CastorStudy $study, string $optionGroupId): FieldOptionGroup
    {
        $body = $this->request('/api/study/' . $study->getSourceId() . '/field-optiongroup/' . $optionGroupId);

        return FieldOptionGroup::fromData($body, $study);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getRecord(CastorStudy $study, string $recordId): Record
    {
        $body = $this->request('/api/study/' . $study->getSourceId() . '/record/' . $recordId);

        return Record::fromData($body);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getRecords(CastorStudy $study, bool $extractArchived = false): ArrayCollection
    {
        $pages = 1;
        $records = new ArrayCollection();

        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/record?archived=' . ((int) $extractArchived) . '&page=' . $page . '&page_size=' . $this->pageSize);
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
    private function getRecordStudyData(CastorStudy $study, Record $record): RecordData
    {
        $body = $this->request('/api/study/' . $study->getSourceId() . '/record/' . $record->getId() . '/data-point-collection/study');

        return StudyData::fromData($body['_embedded']['items'], $study, $record);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    private function getRecordReportInstances(CastorStudy $study, Record $record): ArrayCollection
    {
        $reportInstances = new ArrayCollection();

        try {
            $body = $this->request('/api/study/' . $study->getSourceId() . '/record/' . $record->getId() . '/report-instance');

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
    private function getRecordSurveyPackageInstances(CastorStudy $study, Record $record): ArrayCollection
    {
        $surveyPackageInstances = new ArrayCollection();

        $body = $this->request('/api/study/' . $study->getSourceId() . '/surveypackageinstance?record_id=' . $record->getId());

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
    private function getRecordSurveyData(CastorStudy $study, Record $record): InstanceDataCollection
    {
        $surveyPackageInstances = $this->getRecordSurveyPackageInstances($study, $record);
        $surveyData = new SurveyData($record);

        foreach ($surveyPackageInstances as $surveyPackageInstance) {
            /** @var SurveyPackageInstance $surveyPackageInstance */
            $body = $this->request('/api/study/' . $study->getSourceId() . '/record/' . $record->getId() . '/data-point/survey/' . $surveyPackageInstance->getId());
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
    private function getRecordReportData(CastorStudy $study, Record $record): InstanceDataCollection
    {
        $reportInstances = $this->getRecordReportInstances($study, $record);
        $body = $this->request('/api/study/' . $study->getSourceId() . '/record/' . $record->getId() . '/data-point-collection/report-instance');

        return ReportData::fromData($body['_embedded']['items'], $study, $record, $reportInstances);
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws SessionTimedOut
     * @throws NoAccessPermission
     * @throws NotFound
     */
    public function getRecordDataCollection(CastorStudy $study, Record $record): Record
    {
        $dataCollection = new RecordDataCollection($record);

        $dataCollection->setStudyData($this->getRecordStudyData($study, $record));
        $dataCollection->setReportData($this->getRecordReportData($study, $record));
        $dataCollection->setSurveyData($this->getRecordSurveyData($study, $record));

        $record->setData($dataCollection);

        return $record;
    }

    /**
     * @throws ErrorFetchingCastorData
     * @throws NoAccessPermission
     * @throws NotFound
     * @throws SessionTimedOut
     */
    public function getStructure(CastorStudy $study): StructureCollection
    {
        $structure = new StructureCollection();

        $structure->setPhases($this->getPhasesAndSteps($study, false));
        $structure->setReports($this->getReports($study, false));
        $structure->setSurveys($this->getSurveys($study, false));

        return $structure;
    }
}
