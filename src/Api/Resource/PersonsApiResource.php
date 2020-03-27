<?php
declare(strict_types=1);

namespace App\Api\Resource;

use App\Entity\FAIRData\Person;

class PersonsApiResource implements ApiResource
{
    /** @var Person[] */
    private $persons;

    /**
     * @param Person[] $persons
     */
    public function __construct(array $persons)
    {
        $this->persons = $persons;
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $data = [];

        foreach ($this->persons as $person) {
            $data[] = (new PersonApiResource($person))->toArray();
        }

        return $data;
    }
}
