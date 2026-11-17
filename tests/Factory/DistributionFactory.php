<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\FAIRData\Distribution;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<Distribution> */
final class DistributionFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return Distribution::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'slug' => self::faker()->unique()->slug(),
            'dataset' => DatasetFactory::new(),
        ];
    }

    public function published(): static
    {
        return $this->with(['isPublished' => true]);
    }
}
