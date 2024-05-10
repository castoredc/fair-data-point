<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\OptionGroup;

class OptionGroupApiResource implements ApiResource
{
    public function __construct(private OptionGroup $optionGroup)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [
            'id' => $this->optionGroup->getId(),
            'title' => $this->optionGroup->getTitle(),
            'description' => $this->optionGroup->getDescription(),
            'options' => [],
        ];

        foreach ($this->optionGroup->getOptions() as $option) {
            $data['options'][] = (new OptionGroupOptionApiResource($option))->toArray();
        }

        return $data;
    }
}
