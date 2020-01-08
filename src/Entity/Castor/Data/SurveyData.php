<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\FieldResult;
use App\Entity\Castor\InstanceDataCollection;
use App\Entity\Castor\Instances\SurveyInstance;
use App\Entity\Castor\Instances\SurveyPackageInstance;
use App\Entity\Castor\Record;
use App\Entity\Castor\RecordData;
use App\Entity\Castor\Study;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Exception;

class SurveyData extends InstanceDataCollection
{
    /** @var ArrayCollection<string, SurveyInstance> */
    private $surveyInstances;

    /** @var ArrayCollection<string, SurveyInstance> */
    private $surveyPackageInstances;

    /**
     * SurveyData constructor.
     */
    public function __construct(Record $record)
    {
        parent::__construct($record);
        $this->surveyInstances = new ArrayCollection();
        $this->surveyPackageInstances = new ArrayCollection();
    }


    public function addSurveyPackageData(array $data, Study $study, SurveyPackageInstance $surveyPackageInstance)
    {
        foreach ($data as $rawInstanceResults) {
            $surveyInstance = $this->getSurveyInstance($rawInstanceResults, $surveyPackageInstance);
            $field = $study->getFields()->get($rawInstanceResults['field_id']);

            $rawInstanceResults['field_value'] = $rawInstanceResults['value'];
            $fieldResult = FieldResult::fromData($rawInstanceResults, $field, $this->record);

            $this->addData($surveyInstance, $fieldResult);
        }
    }

    private function getSurveyInstance(array $rawInstanceResults, SurveyPackageInstance $surveyPackageInstance)
    {
        if($this->surveyInstances->contains($rawInstanceResults['survey_instance_id']))
        {
            return $this->surveyInstances->get($rawInstanceResults['survey_instance_id']);
        }

        $url = $rawInstanceResults['_links']['self']['href'];
        $matches = explode('/', $url);

        $instance = new SurveyInstance($matches[count($matches)-2], $this->record, $surveyPackageInstance->getCreatedOn(), $surveyPackageInstance);
        $this->surveyInstances->set($instance->getId(), $instance);

        return $instance;
    }

    /** @inheritDoc
     * @throws Exception
     */
    public static function fromData(array $data, Study $study, Record $record, ?Collection $instances): InstanceDataCollection
    {
        return new SurveyData($record);
    }
}
