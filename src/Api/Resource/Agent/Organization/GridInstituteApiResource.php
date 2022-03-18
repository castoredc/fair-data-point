<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Organization;

use App\Api\Resource\ApiResource;
use App\Entity\Grid\Institute;

class GridInstituteApiResource implements ApiResource
{
    private Institute $institute;

    public function __construct(Institute $institute)
    {
        $this->institute = $institute;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $coordinates = null;
        $address = $this->institute->getMainAddress();

        if ($address->hasCoordinates()) {
            $coordinates = [
                'lat' => $address->getLat(),
                'long' => $address->getLng(),
            ];
        }

        return [
            'id' => $this->institute->getId(),
            'name' => $this->institute->getName(),
            'country' => $address->getCountryCode(),
            'city' => $address->getCity(),
            'homepage' => $this->institute->hasLinks() ? $this->institute->getLinks()[0]->getValue() : null,
            'coordinates' => $coordinates,
        ];
    }
}
