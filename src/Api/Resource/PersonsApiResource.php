<?php

namespace App\Api\Resource;

use App\Entity\FAIRData\Person;

class PersonsApiResource implements ApiResource
{
    /** @var Person[] */
    private $persons;

    public function __construct(array $persons)
    {
        $this->persons = $persons;
    }

    public function toArray(): array
    {
        $data = [];

        foreach ($this->persons as $person) {
            $data[] = (new PersonApiResource($person))->toArray();
        }

        return $data;
    }
}