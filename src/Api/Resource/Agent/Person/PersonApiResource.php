<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Person;

use App\Api\Resource\Agent\AgentApiResource;
use App\Entity\FAIRData\Agent\Person;
use function array_merge;
use function assert;

class PersonApiResource extends AgentApiResource
{
    public function __construct(Person $person)
    {
        $this->agent = $person;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $agent = $this->agent;
        assert($agent instanceof Person);

        $affiliations = [];

        foreach ($agent->getAffiliations() as $affiliation) {
            $affiliations[] = (new AffiliationApiResource($affiliation))->toArray();
        }

        return array_merge(parent::toArray(), [
            'type' => 'person',
            'firstName' => $agent->getFirstName(),
            'middleName' => $agent->getMiddleName(),
            'lastName' => $agent->getLastName(),
            'fullName' => $agent->getFullName(),
            'nameOrigin' => $agent->getNameOrigin()->toString(),
            'email' => $agent->getEmail(),
            'orcid' => $agent->getOrcid()?->getValue(),
            'affiliations' => $affiliations,
        ]);
    }
}
