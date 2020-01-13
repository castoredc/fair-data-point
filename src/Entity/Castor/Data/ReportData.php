<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\FieldResult;
use App\Entity\Castor\InstanceDataCollection;
use App\Entity\Castor\Record;
use App\Entity\Castor\Study;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;

class ReportData extends InstanceDataCollection
{
    /**
     * @throws Exception
     *
     * @inheritDoc
     */
    public static function fromData(array $data, Study $study, Record $record, ArrayCollection $instances): InstanceDataCollection
    {
        $reportData = new ReportData($record);

        foreach ($data as $rawInstanceResults) {
            $instance = $instances->get($rawInstanceResults['report_instance_id']);
            $field = $study->getFields()->get($rawInstanceResults['field_id']);
            $fieldResult = FieldResult::fromData($rawInstanceResults, $field, $record);

            $reportData->addInstanceData($instance, $fieldResult);
        }

        return $reportData;
    }
}
