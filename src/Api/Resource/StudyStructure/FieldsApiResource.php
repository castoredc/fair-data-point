<?php

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Form\Field;
use App\Entity\Castor\Structure\StructureCollection\StructureCollection;

class FieldsApiResource implements ApiResource
{
    /** @var Field[] */
    private $fields;

    /**
     * @param Field[] $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->fields as $field) {
            $data[] = (new FieldApiResource($field))->toArray();
        }

        return $data;
    }
}