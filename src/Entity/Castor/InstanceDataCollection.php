<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Data\ReportData;
use App\Entity\Castor\Data\StudyData;
use App\Entity\Castor\Data\SurveyData;
use App\Entity\Castor\Instances\Instance;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

abstract class InstanceDataCollection
{
    /** @var Record */
    protected $record;

    /** @var ArrayCollection<string, InstanceData> */
    private $data;

    public function __construct(Record $record)
    {
        $this->record = $record;
        $this->data = new ArrayCollection();
    }

    public function getMostRecentInstance(): Instance
    {
        /** @var Instance $return */
        $return = $this->data->first()->getInstance();

        foreach($this->data as $instance)
        {
            /** @var InstanceData $instance */
            if($instance->getInstance()->getCreatedOn() > $return->getCreatedOn())
            {
                $return = $instance->getInstance();
            }
        }

        return $return;
    }

    public function getFieldResultByVariableName(string $variableName): ?FieldResult
    {
        $mostRecentInstance = $this->getMostRecentInstance();

        return $this->data->get($mostRecentInstance->getId())->getFieldResultByVariableName($variableName);
    }

    /**
     * @param array<mixed> $data
     *
     * @return StudyData|SurveyData|ReportData
     */
    public static function fromData(array $data, Study $study, Record $record, ?Collection $instances)
    {
        return null;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function addData(Instance $instance, FieldResult $fieldResult): void
    {
        if(!$this->data->containsKey($instance->getId()))
        {
            $this->data->set($instance->getId(), new InstanceData($this->record, $instance));
        }

        /** @var InstanceData $instanceData */
        $instanceData = $this->data->get($instance->getId());
        $instanceData->addData($fieldResult);
    }
}
