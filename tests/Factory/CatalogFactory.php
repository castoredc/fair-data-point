<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\FAIRData\Catalog;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<Catalog> */
final class CatalogFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Catalog::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'slug' => self::faker()->unique()->slug(),
        ];
    }
}
