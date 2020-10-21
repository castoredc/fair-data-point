<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Person;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Person;

class PersonApiResource implements ApiResource
{
    private Person $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $affiliations = [];

        foreach ($this->person->getAffiliations() as $affiliation) {
            $affiliations[] = (new AffiliationApiResource($affiliation))->toArray();
        }

        return [
            'type' => 'person',
            'id' => $this->person->getId(),
            'name' => $this->person->getName(),
            'firstName' => $this->person->getFirstName(),
            'middleName' => $this->person->getMiddleName(),
            'lastName' => $this->person->getLastName(),
            'fullName' => $this->person->getFullName(),
            'nameOrigin' => $this->person->getNameOrigin()->toString(),
            'email' => $this->person->getEmail(),
            'orcid' => $this->person->getOrcid() !== null ? $this->person->getOrcid()->getValue() : null,
            'affiliations' => $affiliations,
        ];
    }
}
