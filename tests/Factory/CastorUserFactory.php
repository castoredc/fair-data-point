<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Security\Providers\Castor\CastorUser;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<CastorUser> */
final class CastorUserFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return CastorUser::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'id' => self::faker()->uuid(),
            'nameFirst' => self::faker()->firstName(),
            'nameMiddle' => null,
            'nameLast' => self::faker()->lastName(),
            'emailAddress' => self::faker()->unique()->safeEmail(),
        ];
    }
}
