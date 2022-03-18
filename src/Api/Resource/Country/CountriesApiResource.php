<?php
declare(strict_types=1);

namespace App\Api\Resource\Country;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Country;

class CountriesApiResource implements ApiResource
{
    /** @var Country[] */
    private array $countries;

    /** @param Country[] $countries */
    public function __construct(array $countries)
    {
        $this->countries = $countries;
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
