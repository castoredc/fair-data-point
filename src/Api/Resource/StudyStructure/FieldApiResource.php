<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Form\Field;

class FieldApiResource implements ApiResource
{
    public function __construct(private Field $field)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->field->getId(),
            'type' => $this->field->getType(),
            'label' => $this->field->getFieldLabel(),
            'number' => $this->field->getNumber(),
            'variableName' => $this->field->getVariableName(),
            'required' => $this->field->getRequired(),
            'hidden' => $this->field->getHidden(),
            'info' => $this->field->getInfo(),
            'units' => $this->field->getUnits(),
            'exportable' => [
                'exportable' => $this->field->isExportable(),
                'annotated' => $this->field->isExportableAnnotated(),
                'plain' => $this->field->isExportablePlain(),
                'dataTypes' => $this->field->getSupportedDataTypes(),
            ],
        ];
    }
}
