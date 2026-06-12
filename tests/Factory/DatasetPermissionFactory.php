<?php
declare(strict_types=1);

namespace App\Tests\Factory;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Permission\DatasetPermission;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/** @extends PersistentObjectFactory<DatasetPermission> */
final class DatasetPermissionFactory extends PersistentObjectFactory
{
    public static function class(): string
    {
        return DatasetPermission::class;
    }

    /** @return array<string, mixed> */
    protected function defaults(): array
    {
        return [
            'user' => UserFactory::new(),
            'type' => PermissionType::view(),
            'dataset' => DatasetFactory::new(),
        ];
    }
}
