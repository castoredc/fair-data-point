<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Country;

class CountriesApiResource implements ApiResource
{
    /** @var Country[] */
    private $countries;

    /**
     * @param Country[] $countries
     */
    public function __construct(array $countries)
    {
        $this->countries = $countries;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->countries as $country) {
            $data[] = (new CountryApiResource($country))->toArray();
        }

        return $data;
    }
}
