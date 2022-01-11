<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Organization;

use App\Api\Resource\Agent\AgentApiResource;
use App\Entity\FAIRData\Agent\Organization;
use function array_merge;
use function assert;

class OrganizationApiResource extends AgentApiResource
{
    public function __construct(Organization $organization)
    {
        $this->agent = $organization;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $agent = $this->agent;
        assert($agent instanceof Organization);

        $coordinates = null;

        if ($agent->hasCoordinates()) {
            $coordinates = [
                'lat' => $agent->getCoordinatesLatitude(),
                'long' => $agent->getCoordinatesLongitude(),
            ];
        }

        return array_merge(parent::toArray(), [
            'type' => 'organization',
            'country' => $agent->getCountry()->getCode(),
            'city' => $agent->getCity(),
            'homepage' => $agent->getHomepage() !== null ? $agent->getHomepage()->getValue() : null,
            'coordinates' => $coordinates,
            'source' => 'database',
        ]);
    }
}
