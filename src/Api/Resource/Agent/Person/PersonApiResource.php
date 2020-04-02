<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Person;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Person;

class PersonApiResource implements ApiResource
{
    /** @var Person */
    private $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->person->getName(),
            'firstName' => $this->person->getFirstName(),
            'middleName' => $this->person->getMiddleName(),
            'lastName' => $this->person->getLastName(),
            'email' => $this->person->getEmail(),
            'orcid' => $this->person->getOrcid() !== null ? $this->person->getOrcid()->getValue() : null,
        ];
    }
}
