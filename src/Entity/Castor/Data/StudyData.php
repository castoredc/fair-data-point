<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Record;
use App\Entity\Castor\CastorStudy;
use Exception;

class StudyData extends RecordData
{
    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     *
     * @inheritDoc
     */
    public static function fromData(array $data, CastorStudy $study, Record $record): RecordData
    {
        $recordData = new StudyData($record);

        foreach ($data as $rawFieldResults) {
            $field = $study->getFields()->get($rawFieldResults['field_id']);
            $fieldResult = FieldResult::fromData($rawFieldResults, $field, $record);

            $recordData->addData($fieldResult);
        }

        return $recordData;
    }
}
