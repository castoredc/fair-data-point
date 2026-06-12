<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\FAIRData\Dataset;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<Dataset> */
final class DatasetFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Dataset::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'slug' => self::faker()->unique()->slug(),
        ];
    }

    public function published(): static
    {
        return $this->with(['isPublished' => true]);
    }
}
