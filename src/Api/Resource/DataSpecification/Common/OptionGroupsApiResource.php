<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\Model\ModelVersion;

class OptionGroupsApiResource implements ApiResource
{
    private ModelVersion $dataSpecification;

    public function __construct(ModelVersion $dataSpecification)
    {
        $this->dataSpecification = $dataSpecification;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->dataSpecification->getOptionGroups() as $optionGroup) {
            $data[] = (new OptionGroupApiResource($optionGroup))->toArray();
        }

        return $data;
    }
}
