<?php
declare(strict_types=1);

namespace App\Api\Resource\Country;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Country;

class CountryApiResource implements ApiResource
{
    public function __construct(private Country $country)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'value' => $this->country->getCode(),
            'label' => $this->country->getName(),
        ];
    }
}
