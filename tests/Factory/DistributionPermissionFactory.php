<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Permission\DistributionPermission;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<DistributionPermission> */
final class DistributionPermissionFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return DistributionPermission::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'type' => PermissionType::view(),
            'distribution' => DistributionFactory::new(),
        ];
    }
}
