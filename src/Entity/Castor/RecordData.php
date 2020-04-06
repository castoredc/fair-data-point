<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use Doctrine\Common\Collections\ArrayCollection;

abstract class RecordData
{
    /** @var Record */
    protected $record;

    /** @var ArrayCollection<string, FieldResult> */
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

    public function getFieldResultByFieldId(string $fieldId): ?FieldResult
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
        $this->data->set($fieldResult->getField()->getId(), $fieldResult);
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
