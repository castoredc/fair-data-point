<?php
declare(strict_types=1);

namespace App\Api\Resource\Country;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Country;

class CountriesApiResource implements ApiResource
{
    /** @param Country[] $countries */
    public function __construct(private array $countries)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->countries as $country) {
            $data[] = (new CountryApiResource($country))->toArray();
        }

        return $data;
    }
}
