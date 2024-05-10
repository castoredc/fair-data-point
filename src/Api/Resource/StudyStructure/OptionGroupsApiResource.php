<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Model\Castor\CastorEntityCollection;

class OptionGroupsApiResource implements ApiResource
{
    public function __construct(private CastorEntityCollection $optionGroups)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->optionGroups as $field) {
            $data[] = (new OptionGroupApiResource($field))->toArray();
        }

        return $data;
    }
}
