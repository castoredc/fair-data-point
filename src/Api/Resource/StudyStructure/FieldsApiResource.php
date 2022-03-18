<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Form\Field;

class FieldsApiResource implements ApiResource
{
    /** @var Field[] */
    private array $fields;

    /** @param Field[] $fields */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->fields as $field) {
            $data[] = (new FieldApiResource($field))->toArray();
        }

        return $data;
    }
}
