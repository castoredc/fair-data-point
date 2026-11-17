<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Security\User;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<User> */
final class UserFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return User::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'person' => PersonFactory::new(),
        ];
    }
}
