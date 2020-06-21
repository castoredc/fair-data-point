<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Record;
use Doctrine\Common\Collections\ArrayCollection;

abstract class RecordData
{
    /** @var Record */
    protected $record;

    /** @var ArrayCollection<FieldResult[]> */
    private $data;

    /** @var Field[] */
    private $fields;

    public function __construct(Record $record)
    {
        $this->record = $record;
        $this->data = new ArrayCollection();
        $this->fields = [];
    }

    public function getFieldResultByVariableName(string $variableName): ?FieldResult
    {
        $field = $this->getFieldByVariableName($variableName);

        return $field !== null ? $this->data->get($field->getId()) : null;
    }

    /** @return FieldResult[]|null */
    public function getFieldResultsByFieldId(string $fieldId): ?array
    {
        return $this->data->get($fieldId);
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function addData(FieldResult $fieldResult): void
    {
        $this->fields[] = $fieldResult->getField();
        $fieldId = $fieldResult->getField()->getId();

        $results = $this->data->containsKey($fieldId) ? $this->data->get($fieldId) : [];
        $results[] = $fieldResult;

        $this->data->set($fieldId, $results);
    }

    private function getFieldByVariableName(string $variableName): ?Field
    {
        foreach ($this->fields as $field) {
            if ($field->getVariableName() === $variableName) {
                return $field;
            }
        }

        return null;
    }
}
