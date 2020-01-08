<?php
declare(strict_types=1);

namespace App\Entity\Castor\Instances;

use App\Entity\Castor\Record;
use DateTime;

class SurveyInstance extends Instance
{
    /** @var SurveyPackageInstance */
    private $surveyPackageInstance;

    public function __construct(string $id, Record $record, DateTime $createdOn, SurveyPackageInstance $surveyPackageInstance)
    {
        parent::__construct($id, $record, $createdOn);

        $this->surveyPackageInstance = $surveyPackageInstance;
    }

    public function getSurveyPackageInstance(): SurveyPackageInstance
    {
        return $this->surveyPackageInstance;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data, Record $record, SurveyPackageInstance $surveyPackageInstance): SurveyInstance
    {
        return new SurveyInstance(
            $data['id'],
            $record,
            $surveyPackageInstance->getCreatedOn(),
            $surveyPackageInstance
        );
    }
}
