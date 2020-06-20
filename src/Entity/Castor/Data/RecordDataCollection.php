<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Record;
use Symfony\Component\Config\Definition\Exception\InvalidTypeException;
use function is_a;

class RecordDataCollection
{
    /** @var StudyData */
    private $studyData;

    /** @var SurveyData */
    private $surveyData;

    /** @var ReportData */
    private $reportData;

    /** @var Record */
    private $record;

    public function __construct(Record $record)
    {
        $this->record = $record;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function getStudy(): StudyData
    {
        return $this->studyData;
    }

    public function getSurvey(): SurveyData
    {
        return $this->surveyData;
    }

    public function getReport(): ReportData
    {
        return $this->reportData;
    }

    public function setStudyData(RecordData $studyData): void
    {
        if (! is_a($studyData, StudyData::class)) {
            throw new InvalidTypeException('Data should be of type StudyData');
        }
        $this->studyData = $studyData;
    }

    public function setSurveyData(InstanceDataCollection $surveyData): void
    {
        if (! is_a($surveyData, SurveyData::class)) {
            throw new InvalidTypeException('Data should be of type SurveyData');
        }
        $this->surveyData = $surveyData;
    }

    public function setReportData(InstanceDataCollection $reportData): void
    {
        if (! is_a($reportData, ReportData::class)) {
            throw new InvalidTypeException('Data should be of type ReportData');
        }
        $this->reportData = $reportData;
    }

    /** @return FieldResult[]|null */
    public function getFieldResultByFieldId(string $fieldId): ?array
    {
        $study = $this->studyData->getFieldResultsByFieldId($fieldId);
        $survey = $this->surveyData->getFieldResultsByFieldId($fieldId);
        $report = $this->reportData->getFieldResultsByFieldId($fieldId);

        if ($study !== null) {
            return $study;
        }

        if ($survey !== null) {
            return $survey;
        }

        if ($report !== null) {
            return $report;
        }

        return null;
    }
}
