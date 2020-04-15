<?php
declare(strict_types=1);

namespace App\Entity\Castor\Structure\StructureCollection;

use App\Entity\Castor\Structure\Phase;
use App\Entity\Castor\Structure\Report;
use App\Entity\Castor\Structure\Survey;

class StructureCollection
{
    /** @var PhaseCollection */
    private $phases;

    /** @var ReportCollection */
    private $reports;

    /** @var SurveyCollection */
    private $surveys;

    public function __construct()
    {
        $this->phases = new PhaseCollection();
        $this->reports = new ReportCollection();
        $this->surveys = new SurveyCollection();
    }

    public function addPhase(Phase $phase): void
    {
        $this->phases->add($phase);
    }

    public function addReport(Report $report): void
    {
        $this->reports->add($report);
    }

    public function addSurvey(Survey $survey): void
    {
        $this->surveys->add($survey);
    }

    public function getPhases(): PhaseCollection
    {
        return $this->phases;
    }

    public function getReports(): ReportCollection
    {
        return $this->reports;
    }

    public function getSurveys(): SurveyCollection
    {
        return $this->surveys;
    }

    public function setPhases(PhaseCollection $phases): void
    {
        $this->phases = $phases;
    }

    public function setReports(ReportCollection $reports): void
    {
        $this->reports = $reports;
    }

    public function setSurveys(SurveyCollection $surveys): void
    {
        $this->surveys = $surveys;
    }

    public function order(): void
    {
        $this->phases->order();
        $this->reports->order();
        $this->surveys->order();
    }
}
