<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Form\Field;

class FieldApiResource implements ApiResource
{
    /** @var Field */
    private $field;

    public function __construct(Field $field)
    {
        $this->field = $field;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->field->getId(),
            'type' => $this->field->getType(),
            'label' => $this->field->getLabel(),
            'number' => $this->field->getNumber(),
            'variableName' => $this->field->getVariableName(),
            'required' => $this->field->getRequired(),
            'hidden' => $this->field->getHidden(),
            'info' => $this->field->getInfo(),
            'units' => $this->field->getUnits(),
        ];
    }
}
