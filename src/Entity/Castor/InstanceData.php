<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Data\ReportData;
use App\Entity\Castor\Data\StudyData;
use App\Entity\Castor\Data\SurveyData;
use App\Entity\Castor\Instances\Instance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class InstanceData extends RecordData
{
    /** @var Record */
    protected $record;

    /** @var Instance */
    protected $instance;

    /** @var ArrayCollection<string, FieldResult> */
    private $data;

    public function __construct(Record $record, Instance $instance)
    {
        parent::__construct($record);

        $this->instance = $instance;
        $this->data = new ArrayCollection();
    }

    public function getFieldResultByVariableName(string $variableName): ?FieldResult
    {
        return $this->data->get($variableName);
    }

    /**
     * @param array<mixed> $data
     *
     * @return StudyData|SurveyData|ReportData
     */
    public static function fromData(array $data, Study $study, Record $record)
    {
        return null;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function getInstance(): Instance
    {
        return $this->instance;
    }

    public function addData(FieldResult $fieldResult): void
    {
        $this->data->set($fieldResult->getField()->getVariableName(), $fieldResult);
    }
}
