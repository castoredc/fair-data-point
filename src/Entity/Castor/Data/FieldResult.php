<?php
declare(strict_types=1);

namespace App\Entity\Castor\Data;

use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Record;
use App\Entity\Castor\Structure\MetadataPoint;
use DateTime;
use Exception;

class FieldResult
{
    /** @var Field */
    private $field;

    /** @var string */
    private $value;

    /** @var string|null */
    private $label;

    /** @var DateTime|null */
    private $updatedOn;

    /** @var Record */
    private $record;

    public function __construct(Field $field, string $value, ?string $label, ?DateTime $updatedOn, Record $record)
    {
        $this->field = $field;
        $this->value = $value;
        $this->label = $label;
        $this->updatedOn = $updatedOn;
        $this->record = $record;
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
            /** @var MetadataPoint $metadataPoint */
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
            null,
            $data['updated_on'] !== null ? new DateTime($data['updated_on']) : null,
            $record
        );
    }
}
