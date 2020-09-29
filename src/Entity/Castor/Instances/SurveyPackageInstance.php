<?php
declare(strict_types=1);

namespace App\Entity\Castor\Instances;

use App\Entity\Castor\Record;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class SurveyPackageInstance extends Instance
{
    /** @var ArrayCollection<string, SurveyInstance> */
    private ArrayCollection $surveyInstances;

    public function __construct(string $id, Record $record, DateTime $createdOn)
    {
        parent::__construct($id, $record, $createdOn);
        $this->surveyInstances = new ArrayCollection();
    }

    public function addSurveyInstance(SurveyInstance $surveyInstance): void
    {
        $this->surveyInstances->set($surveyInstance->getId(), $surveyInstance);
    }

    public function getSurveyInstances(): ArrayCollection
    {
        return $this->surveyInstances;
    }

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function fromData(array $data, Record $record): SurveyPackageInstance
    {
        $instance = new SurveyPackageInstance(
            $data['id'],
            $record,
            new DateTime($data['created_on']['date'])
        );

        foreach ($data['_embedded']['survey_package']['_embedded']['surveys'] as $surveyInstance) {
            $instance->addSurveyInstance(SurveyInstance::fromData($surveyInstance, $record, $instance));
        }

        return $instance;
    }
}
