<?php
declare(strict_types=1);

namespace App\Tests\unit\Entity\FAIRData;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Catalog;
use App\Security\User;
use PHPUnit\Framework\TestCase;

final class CatalogTest extends TestCase
{
    public function testReturnsPermissionForUserItWasAddedFor(): void
    {
        $catalog = new Catalog('my-catalog');
        $user = new User(null);

        $permission = $catalog->addPermissionForUser($user, PermissionType::edit());

        self::assertSame($permission, $catalog->getPermissionsForUser($user));
        self::assertSame($user, $permission->getUser());
        self::assertSame($catalog, $permission->getEntity());
        self::assertTrue($permission->getType()->isEdit());
    }

    public function testReturnsNullForUserWithoutPermission(): void
    {
        $catalog = new Catalog('my-catalog');
        $userWithPermission = new User(null);
        $otherUser = new User(null);

        $catalog->addPermissionForUser($userWithPermission, PermissionType::view());

        self::assertNull($catalog->getPermissionsForUser($otherUser));
    }

    public function testReturnsNullWhenNoPermissionsExist(): void
    {
        $catalog = new Catalog('my-catalog');

        self::assertNull($catalog->getPermissionsForUser(new User(null)));
    }

    public function testReturnsMatchingPermissionPerUserWhenMultiplePermissionsExist(): void
    {
        $catalog = new Catalog('my-catalog');
        $viewer = new User(null);
        $manager = new User(null);

        $viewerPermission = $catalog->addPermissionForUser($viewer, PermissionType::view());
        $managerPermission = $catalog->addPermissionForUser($manager, PermissionType::manage());

        self::assertSame($viewerPermission, $catalog->getPermissionsForUser($viewer));
        self::assertSame($managerPermission, $catalog->getPermissionsForUser($manager));
    }

    public function testReturnsFirstAddedPermissionWhenUserHasMultiplePermissions(): void
    {
        $catalog = new Catalog('my-catalog');
        $user = new User(null);

        $firstPermission = $catalog->addPermissionForUser($user, PermissionType::view());
        $catalog->addPermissionForUser($user, PermissionType::manage());

        self::assertSame($firstPermission, $catalog->getPermissionsForUser($user));
        self::assertCount(2, $catalog->getPermissions());
    }

    public function testRemovePermissionForUserOnlyRemovesThatUsersPermission(): void
    {
        $catalog = new Catalog('my-catalog');
        $userToRemove = new User(null);
        $otherUser = new User(null);

        $catalog->addPermissionForUser($userToRemove, PermissionType::edit());
        $otherPermission = $catalog->addPermissionForUser($otherUser, PermissionType::view());

        $catalog->removePermissionForUser($userToRemove);

        self::assertNull($catalog->getPermissionsForUser($userToRemove));
        self::assertSame($otherPermission, $catalog->getPermissionsForUser($otherUser));
        self::assertCount(1, $catalog->getPermissions());
    }

    public function testRemovePermissionForUserWithoutPermissionLeavesPermissionsUntouched(): void
    {
        $catalog = new Catalog('my-catalog');
        $user = new User(null);
        $permission = $catalog->addPermissionForUser($user, PermissionType::edit());

        $catalog->removePermissionForUser(new User(null));

        self::assertSame($permission, $catalog->getPermissionsForUser($user));
        self::assertCount(1, $catalog->getPermissions());
    }
}
