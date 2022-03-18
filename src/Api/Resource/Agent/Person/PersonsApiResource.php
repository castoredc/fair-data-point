<?php
declare(strict_types=1);

namespace App\Api\Resource\Agent\Person;

use App\Api\Resource\ApiResource;
use App\Entity\FAIRData\Agent\Person;

class PersonsApiResource implements ApiResource
{
    /** @var Person[] */
    private array $persons;

    /** @param Person[] $persons */
    public function __construct(array $persons)
    {
        $this->persons = $persons;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->persons as $person) {
            $data[] = (new PersonApiResource($person))->toArray();
        }

        return $data;
    }
}
