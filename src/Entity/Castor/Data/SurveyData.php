<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Instances\SurveyInstance;
use App\Entity\Castor\Instances\SurveyPackageInstance;
use App\Entity\Castor\Record;
use App\Entity\Castor\CastorStudy;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use function count;
use function explode;

class SurveyData extends InstanceDataCollection
{
    /** @var ArrayCollection<string, SurveyInstance> */
    private $surveyInstances;

    public function __construct(Record $record)
    {
        parent::__construct($record);
        $this->surveyInstances = new ArrayCollection();
    }

    /**
     * @param array<mixed> $data
     */
    public function addSurveyPackageData(array $data, CastorStudy $study, SurveyPackageInstance $surveyPackageInstance): void
    {
        foreach ($data as $rawInstanceResults) {
            $surveyInstance = $this->getSurveyInstance($rawInstanceResults, $surveyPackageInstance);
            $field = $study->getFields()->get($rawInstanceResults['field_id']);

            $rawInstanceResults['field_value'] = $rawInstanceResults['value'];
            $fieldResult = FieldResult::fromData($rawInstanceResults, $field, $this->record);

            $this->addInstanceData($surveyInstance, $fieldResult);
        }
    }

    /**
     * @param array<mixed> $rawInstanceResults
     */
    private function getSurveyInstance(array $rawInstanceResults, SurveyPackageInstance $surveyPackageInstance): SurveyInstance
    {
        if ($this->surveyInstances->contains($rawInstanceResults['survey_instance_id'])) {
            return $this->surveyInstances->get($rawInstanceResults['survey_instance_id']);
        }

        $url = $rawInstanceResults['_links']['self']['href'];
        $matches = explode('/', $url);

        $instance = new SurveyInstance($matches[count($matches) - 2], $this->record, $surveyPackageInstance->getCreatedOn(), $surveyPackageInstance);
        $this->surveyInstances->set($instance->getId(), $instance);

        return $instance;
    }

    /**
     * @throws Exception
     *
     * @inheritDoc
     */
    public static function fromData(array $data, CastorStudy $study, Record $record, ArrayCollection $instances): InstanceDataCollection
    {
        return new SurveyData($record);
    }
}
