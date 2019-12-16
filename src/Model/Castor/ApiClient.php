<?php

namespace App\Model\Castor;

use App\Entity\Annotator\Castor\Form\FieldOption;
use App\Entity\Annotator\Castor\Form\FieldOptionGroup;
use App\Entity\Annotator\Castor\Form\Field;
use App\Entity\Annotator\Castor\Structure\Phase;
use App\Entity\Annotator\Castor\Structure\Report;
use App\Entity\Annotator\Castor\Structure\Step\StudyStep;
use App\Entity\Annotator\Castor\Structure\Survey;
use App\Entity\Annotator\Castor\Structure\Step\ReportStep;
use App\Entity\Annotator\Castor\Structure\Step\Step;
use App\Entity\Annotator\Castor\Structure\Step\SurveyStep;
use App\Entity\Castor\Study;
use App\Entity\Annotator\Castor\Study\Institute;
use App\Entity\Annotator\Castor\Study\StudyCollection;
use App\Entity\Castor\User;
use App\Exception\SessionTimeOutException;
use App\Security\CastorUser;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Security;

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
     * @var Field[]
     *
     * Used for caching
     */
    private $study_fields;

    /**
     * ApiClient constructor.
     */
    public function __construct(string $castorEdcUrl)
    {
        $this->client = new Client();
        $this->server = $castorEdcUrl;
    }

    public function auth(string $clientId, string $secret)
    {
        try {
            $response = $this->client->request('POST',
                $this->server . '/oauth/token',
                [
                    'json' => [
                        'client_id' => $clientId,
                        'client_secret' => $secret,
                        'grant_type' => 'client_credentials'
                    ]
                ]
            );
        } catch (GuzzleException $e) {
            throw new AccessDeniedHttpException($e->getMessage());
        }

        $data = json_decode($response->getBody(), true);
        $this->token = $data['access_token'];
    }

    /**
     * @return StudyCollection
     * @throws Exception
     */
    public function getStudies()
    {
        $studies = new StudyCollection();
        $body = $this->request('/api/study');

        foreach ($body['_embedded']['study'] as $study) {
            $studies->add(
                Study::fromData($study)
            );
        }
        return $studies;
    }

    /**
     * @param $uri
     * @return mixed
     * @throws Exception
     */
    private function request($uri)
    {
        try {
            $response = $this->client->request(
                'GET',
                $this->server . $uri,
                [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->token,
                        'Accept' => 'application/json'
                    ]
                ]
            );

            $body = json_decode($response->getBody(), true);
        } catch (RequestException $e) {
            if($e->getCode() == 401)
            {
                throw new UnauthorizedHttpException('', $e->getMessage());
            }
            else if($e->getCode() == 403)
            {
                throw new UnauthorizedHttpException('', 'You do not have permission to access this');
            }

            throw new HttpException(500, $e->getMessage());
        } catch (GuzzleException $e) {
            throw new HttpException(500, $e->getMessage());
        }

        return $body;
    }

    /**
     * @param string $studyId
     * @return ArrayCollection
     * @throws Exception
     */
    public function getInstitutes(string $studyId)
    {
        $institutes = new ArrayCollection();
        $body = $this->request('/api/study/' . $studyId . '/institute');
        foreach ($body['_embedded']['institutes'] as $institute) {
            $institutes->add(
                Institute::fromData($institute)
            );
        }
        return $institutes;
    }

    /**
     * @param string $studyId
     * @return Study
     * @throws Exception
     */
    public function getStudy(string $studyId)
    {
        $study = $this->request('/api/study/' . $studyId);

        return Study::fromData($study);
    }

    public function getFieldByParent(string $studyId, $parentId)
    {
        $fields = $this->getFields($studyId);
        $results = new ArrayCollection();

        foreach($fields->getIterator() as $field)
        {
            /**
             * @var Field $field
             */
            if($field->getParentId() == $parentId)
            {
                $results->set($field->getId(), $field);
            }
        }

        return $results;
    }

    /**
     * @param string $studyId
     * @param bool $cache
     * @return Field[]|ArrayCollection
     * @throws Exception
     */
    public function getFields(string $studyId, bool $cache = true)
    {
        if($cache && isset($this->study_fields))
        {
            return $this->study_fields;
        }

        $pages = 1;
        $fields = new ArrayCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $studyId . '/field?page=' . $page . '&include=optiongroup|metadata&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['fields'] as $field) {
                $field = Field::fromData($field);

                # ToDo add option group if(isset($field['option_group']['id']))
                $fields->set($field->getId(), $field);
            }
        }

        $this->study_fields = $fields;

        return $fields;
    }

    public function getStructure(string $studyId, bool $includeFields = false)
    {
        $phases = $this->getPhasesAndSteps($studyId, $includeFields);
//        $surveys = $this->getSurveys($studyId, $includeFields);
        $reports = $this->getReports($studyId, $includeFields);

        $structure = [
            'phase' => $phases,
//            'survey' => $surveys,
            'report' => $reports
        ];

        return $structure;
    }

    public function getPhasesAndSteps(string $studyId, bool $includeFields = false)
    {
        $return = [];

        $phases = $this->getPhases($studyId);
        $steps = $this->getStudySteps($studyId, $includeFields);

        foreach ($steps->getIterator() as $step) {
            /**
             * @var StudyStep $step
             * @var Phase $phase
             */
            $phase = $phases->get($step->getParentId());
            $step->setParent($phase);
            $phase->addStep($step);
        }

        foreach($phases->getIterator() as $phase)
        {
            $return[$phase->getPosition()] = $phase;
        }

        ksort($return);

        return $return;
    }

    /**
     * @param string $studyId
     * @return ArrayCollection
     * @throws Exception
     */
    public function getPhases(string $studyId)
    {
        $pages = 1;
        $phases = new ArrayCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $studyId . '/phase?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['phases'] as $phase) {
                $phases->set(
                    $phase['phase_id'],
                    Phase::fromData($phase)
                );
            }

        }
        return $phases;
    }

    public function getStudySteps(string $studyId, bool $includeFields = false)
    {
        $pages = 1;
        $steps = new ArrayCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $studyId . '/step?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['steps'] as $step) {
                $newStep = StudyStep::fromData($step);

                if($includeFields)
                {
                    $fields = $this->getFieldByParent($studyId, $step['step_id']);
                    $newStep->setFields($fields->toArray());
                }

                $steps->set($step['step_id'], $newStep);
            }
        }

        return $steps;
    }

    /**
     * @param string $studyId
     * @param bool $includeFields
     * @return ArrayCollection
     * @throws Exception
     */
    public function getSurveys(string $studyId, bool $includeFields = false)
    {
        $pages = 1;
        $surveys = new ArrayCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $studyId . '/survey?include=steps&page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['surveys'] as $survey) {
                $tempSurvey = Survey::fromData($survey);

                if($includeFields)
                {
                    $steps = new ArrayCollection();
                    foreach($tempSurvey->getSteps() as $surveyStep) {
                        $fields = $this->getFieldByParent($studyId, $surveyStep->getId());
                        $surveyStep->setFields($fields->toArray());
                        $steps->add($surveyStep);
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
     * @param string $studyId
     * @param bool $includeFields
     * @return ArrayCollection
     * @throws Exception
     */
    public function getReports(string $studyId, bool $includeFields = false)
    {
        $pages = 1;
        $reports = new ArrayCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $studyId . '/report?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['reports'] as $report) {
                $tempReport = Report::fromData($report);
                $steps = $this->request('/api/study/' . $studyId . '/report/' . $report['report_id'] . '/report-step?page=' . $page . '&page_size=' . $this->pageSize);

                foreach ($steps['_embedded']['report_steps'] as $step) {
                    $newStep = ReportStep::fromData($step);

                    if($includeFields)
                    {
                        $fields = $this->getFieldByParent($studyId, $step['id']);
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
     * @param string $studyId
     * @return ArrayCollection
     * @throws Exception
     */
    public function getOptionGroups(string $studyId)
    {
        $pages = 1;
        $optionGroups = new ArrayCollection();
        for ($page = 1; $page <= $pages; $page++) {
            $body = $this->request('/api/study/' . $studyId . '/field-optiongroup?page=' . $page . '&page_size=' . $this->pageSize);
            $pages = $body['page_count'];

            foreach ($body['_embedded']['fieldOptionGroups'] as $optionGroup) {
                $optionGroups->add(FieldOptionGroup::fromData($optionGroup));
            }
        }
        return $optionGroups;
    }

    /**
     * @param string $studyId
     * @param string $recordId
     * @return mixed
     * @throws Exception
     */
    public function getRecordDataPoints(string $studyId, string $recordId)
    {
        $body = $this->request('/api/study/' . $studyId . '/record/' . $recordId . '/data-point-collection/study');
        $dataPoints = $body['_embedded']['items'];

        return $dataPoints;
    }

    /**
     * @return User
     * @throws Exception
     */
    public function getUser()
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


    public function getRawMetadata(string $studyId)
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

    public function getRawFields(string $studyId)
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

    public function getRawRecords(string $studyId)
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
        $return = [];
        foreach($records as $record)
        {
            $return[$record['record_id']] = $record;
        }
        return $return;
    }

    public function getRawRecordDataPoints(string $studyId, string $recordId)
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