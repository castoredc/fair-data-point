<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\Study;

class StudyMapApiResource implements ApiResource
{
    public function __construct(private Study $study, private Organization $organization)
    {
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $metadata = $this->study->getLatestMetadata();

        $coordinates = [
            'lat' => $this->organization->getCoordinatesLatitude(),
            'long' => $this->organization->getCoordinatesLongitude(),
        ];

        return [
            'title' => $metadata->getBriefName(),
            'relativeUrl' => $this->study->getRelativeUrl(),
            'organization' => $this->organization->getName(),
            'city' => $this->organization->getCity(),
            'country' => $this->organization->getCountry()->getName(),
            'coordinates' => $coordinates,
        ];
    }
}
