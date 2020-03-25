<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Person;

class PersonApiResource implements ApiResource
{
    /** @var Person */
    private $person;

    public function __construct(Person $person)
    {
        $this->person = $person;
    }

    public function toArray(): array
    {
        return [
            'firstName' => $this->person->getFirstName(),
            'middleName' => $this->person->getMiddleName(),
            'lastName' => $this->person->getLastName(),
            'email' => $this->person->getEmail(),
            'orcid' => $this->person->getOrcid()->getValue(),
        ];
    }
}
