<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Castor\CastorStudy;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<CastorStudy> */
final class CastorStudyFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return CastorStudy::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'sourceId' => self::faker()->uuid(),
            'name' => self::faker()->sentence(3),
            'slug' => self::faker()->unique()->slug(),
        ];
    }
}
