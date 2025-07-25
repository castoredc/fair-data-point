<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Record;
use DateTime;
use Exception;

class FieldResult
{
    public function __construct(private Field $field, private string $value, private Record $record, private ?string $label = null, private ?DateTime $updatedOn = null)
    {
    }

    public function getField(): Field
    {
        return $this->field;
    }

    public function setField(Field $field): void
    {
        $this->field = $field;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): void
    {
        $this->label = $label;
    }

    public function getUpdatedOn(): ?DateTime
    {
        return $this->updatedOn;
    }

    public function setUpdatedOn(?DateTime $updatedOn): void
    {
        $this->updatedOn = $updatedOn;
    }

    public function getRecord(): Record
    {
        return $this->record;
    }

    public function getMetadata(): ?string
    {
        foreach ($this->field->getMetadata() as $metadataPoint) {
            if ($metadataPoint->getDescription() === $this->value) {
                return $metadataPoint->getValue();
            }
        }

        return null;
    }

    public function setRecord(Record $record): void
    {
        $this->record = $record;
    }

    /**
     * @param array<mixed> $data
     *
     * @throws Exception
     */
    public static function fromData(array $data, Field $field, Record $record): FieldResult
    {
        return new FieldResult(
            $field,
            $data['field_value'],
            $record,
            null,
            $data['updated_on'] !== null ? new DateTime($data['updated_on']) : null
        );
    }
}
