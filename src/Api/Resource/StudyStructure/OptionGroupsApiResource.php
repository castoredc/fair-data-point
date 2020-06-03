<?php
declare(strict_types=1);

namespace App\Api\Resource\StudyStructure;

use App\Api\Resource\ApiResource;
use App\Model\Castor\CastorEntityCollection;

class OptionGroupsApiResource implements ApiResource
{
    /** @var CastorEntityCollection */
    private $optionGroups;

    public function __construct(CastorEntityCollection $optionGroups)
    {
        $this->optionGroups = $optionGroups;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->optionGroups as $field) {
            $data[] = (new OptionGroupApiResource($field))->toArray();
        }

        return $data;
    }
}
