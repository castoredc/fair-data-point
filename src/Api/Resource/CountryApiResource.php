<?php

namespace App\Api\Resource;

use App\Entity\FAIRData\Country;

class CountryApiResource implements ApiResource
{
    /** @var Country */
    private $country;

    /**
     * CountryApiResource constructor.
     *
     * @param Country $country
     */
    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->country->getCode(),
            'label' => $this->country->getName()
        ];
    }
}