<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Data\ReportData;
use App\Entity\Castor\Data\StudyData;
use App\Entity\Castor\Data\SurveyData;

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

    public function setStudyData(StudyData $studyData): void
    {
        $this->studyData = $studyData;
    }

    public function setSurveyData(SurveyData $surveyData): void
    {
        $this->surveyData = $surveyData;
    }

    public function setReportData(ReportData $reportData): void
    {
        $this->reportData = $reportData;
    }
}
