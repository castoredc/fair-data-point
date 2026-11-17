<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Permission\CatalogPermission;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<CatalogPermission> */
final class CatalogPermissionFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return CatalogPermission::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'type' => PermissionType::view(),
            'catalog' => CatalogFactory::new(),
        ];
    }
}
