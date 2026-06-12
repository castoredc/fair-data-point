<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Enum\NameOrigin;
use App\Entity\FAIRData\Agent\Person;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<Person> */
final class PersonFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Person::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'firstName' => self::faker()->firstName(),
            'middleName' => null,
            'lastName' => self::faker()->lastName(),
            'email' => self::faker()->unique()->safeEmail(),
            'phoneNumber' => null,
            'orcid' => null,
            'nameOrigin' => NameOrigin::user(),
        ];
    }
}
