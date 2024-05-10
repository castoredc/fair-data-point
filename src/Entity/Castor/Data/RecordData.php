<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Record;
use Doctrine\Common\Collections\ArrayCollection;

abstract class RecordData
{
    /** @var ArrayCollection<FieldResult[]> */
    private ArrayCollection $data;

    /** @var Field[] */
    private array $fields = [];

    public function __construct(protected Record $record)
    {
        $this->data = new ArrayCollection();
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
        $fieldId = $fieldResult->getField()->getId();

        $this->data->set($fieldId, $fieldResult);
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
