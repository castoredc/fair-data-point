<?php
declare(strict_types=1);

namespace App\Api\Resource\Study;

use App\Api\Resource\ApiResource;
use App\Entity\Castor\Study;
use App\Entity\FAIRData\Organization;

class StudyMapApiResource implements ApiResource
{
    /** @var Study */
    private $study;

    /** @var Organization */
    private $organization;

    public function __construct(Study $study, Organization $organization)
    {
        $this->study = $study;
        $this->organization = $organization;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $metadata = $this->study->getLatestMetadata();

        $coordinates = [
            'lat' => $this->organization->getCoordinatesLatitude(),
            'long' => $this->organization->getCoordinatesLongitude(),
        ];

        return [
            'title' => $metadata->getBriefName(),
            'relative_url' => $this->study->getSlug(),
            'organization' => $this->organization->getName(),
            'city' => $this->organization->getCity(),
            'country' => $this->organization->getCountry()->getName(),
            'coordinates' => $coordinates,
        ];
    }
}
