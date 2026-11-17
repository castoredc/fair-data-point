<?php
declare(strict_types=1);

namespace App\Tests\unit\Entity\FAIRData;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Security\User;
use PHPUnit\Framework\TestCase;

final class DatasetTest extends TestCase
{
    public function testReturnsPermissionForUserItWasAddedFor(): void
    {
        $dataset = new Dataset('my-dataset');
        $user = new User(null);

        $permission = $dataset->addPermissionForUser($user, PermissionType::accessData());

        self::assertSame($permission, $dataset->getPermissionsForUser($user));
        self::assertSame($user, $permission->getUser());
        self::assertSame($dataset, $permission->getEntity());
        self::assertTrue($permission->getType()->isAccessData());
    }

    public function testReturnsNullForUserWithoutPermission(): void
    {
        $dataset = new Dataset('my-dataset');
        $userWithPermission = new User(null);
        $otherUser = new User(null);

        $dataset->addPermissionForUser($userWithPermission, PermissionType::view());

        self::assertNull($dataset->getPermissionsForUser($otherUser));
    }

    public function testReturnsNullWhenNoPermissionsExist(): void
    {
        $dataset = new Dataset('my-dataset');

        self::assertNull($dataset->getPermissionsForUser(new User(null)));
    }

    public function testReturnsMatchingPermissionPerUserWhenMultiplePermissionsExist(): void
    {
        $dataset = new Dataset('my-dataset');
        $viewer = new User(null);
        $editor = new User(null);

        $viewerPermission = $dataset->addPermissionForUser($viewer, PermissionType::view());
        $editorPermission = $dataset->addPermissionForUser($editor, PermissionType::edit());

        self::assertSame($viewerPermission, $dataset->getPermissionsForUser($viewer));
        self::assertSame($editorPermission, $dataset->getPermissionsForUser($editor));
    }

    public function testRemovePermissionForUserOnlyRemovesThatUsersPermission(): void
    {
        $dataset = new Dataset('my-dataset');
        $userToRemove = new User(null);
        $otherUser = new User(null);

        $dataset->addPermissionForUser($userToRemove, PermissionType::edit());
        $otherPermission = $dataset->addPermissionForUser($otherUser, PermissionType::view());

        $dataset->removePermissionForUser($userToRemove);

        self::assertNull($dataset->getPermissionsForUser($userToRemove));
        self::assertSame($otherPermission, $dataset->getPermissionsForUser($otherUser));
        self::assertCount(1, $dataset->getPermissions());
    }
}
