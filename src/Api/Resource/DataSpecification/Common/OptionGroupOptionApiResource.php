<?php
declare(strict_types=1);

namespace App\Api\Resource\DataSpecification\Common;

use App\Api\Resource\ApiResource;
use App\Entity\DataSpecification\Common\OptionGroupOption;

class OptionGroupOptionApiResource implements ApiResource
{
    public function __construct(private OptionGroupOption $optionGroupOption)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->optionGroupOption->getId(),
            'title' => $this->optionGroupOption->getTitle(),
            'description' => $this->optionGroupOption->getDescription(),
            'value' => $this->optionGroupOption->getValue(),
            'order' => $this->optionGroupOption->getOrder(),
        ];
    }
}
