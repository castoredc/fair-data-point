<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Country;

class CountryApiResource implements ApiResource
{
    /** @var Country */
    private $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function toArray(): array
    {
        return [
            'value' => $this->country->getCode(),
            'label' => $this->country->getName(),
        ];
    }
}
