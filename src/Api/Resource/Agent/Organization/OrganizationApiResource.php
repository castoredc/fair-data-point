<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Organization;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Organization;

class OrganizationApiResource implements ApiResource
{
    private Organization $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $coordinates = null;

        if ($this->organization->hasCoordinates()) {
            $coordinates = [
                'lat' => $this->organization->getCoordinatesLatitude(),
                'long' => $this->organization->getCoordinatesLongitude(),
            ];
        }

        return [
            'id' => $this->organization->getId(),
            'name' => $this->organization->getName(),
            'country' => $this->organization->getCountry()->getCode(),
            'city' => $this->organization->getCity(),
            'homepage' => $this->organization->getHomepage() !== null ? $this->organization->getHomepage()->getValue() : null,
            'coordinates' => $coordinates,
        ];
    }
}
