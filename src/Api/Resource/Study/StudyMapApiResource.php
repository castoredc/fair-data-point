<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\Study;

class StudyMapApiResource implements ApiResource
{
    private Study $study;

    private Organization $organization;

    public function __construct(Study $study, Organization $organization)
    {
        $this->study = $study;
        $this->organization = $organization;
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
