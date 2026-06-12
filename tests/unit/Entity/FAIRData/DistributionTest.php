<?php
declare(strict_types=1);

namespace App\Tests\unit\Entity\FAIRData;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Security\User;
use PHPUnit\Framework\TestCase;

final class DistributionTest extends TestCase
{
    public function testReturnsPermissionForUserItWasAddedFor(): void
    {
        $distribution = self::distribution();
        $user = new User(null);

        $permission = $distribution->addPermissionForUser($user, PermissionType::manage());

        self::assertSame($permission, $distribution->getPermissionsForUser($user));
        self::assertSame($user, $permission->getUser());
        self::assertSame($distribution, $permission->getEntity());
        self::assertTrue($permission->getType()->isManage());
    }

    public function testReturnsNullForUserWithoutPermission(): void
    {
        $distribution = self::distribution();
        $userWithPermission = new User(null);
        $otherUser = new User(null);

        $distribution->addPermissionForUser($userWithPermission, PermissionType::view());

        self::assertNull($distribution->getPermissionsForUser($otherUser));
    }

    public function testReturnsNullWhenNoPermissionsExist(): void
    {
        self::assertNull(self::distribution()->getPermissionsForUser(new User(null)));
    }

    public function testReturnsMatchingPermissionPerUserWhenMultiplePermissionsExist(): void
    {
        $distribution = self::distribution();
        $viewer = new User(null);
        $dataAccessor = new User(null);

        $viewerPermission = $distribution->addPermissionForUser($viewer, PermissionType::view());
        $dataAccessorPermission = $distribution->addPermissionForUser($dataAccessor, PermissionType::accessData());

        self::assertSame($viewerPermission, $distribution->getPermissionsForUser($viewer));
        self::assertSame($dataAccessorPermission, $distribution->getPermissionsForUser($dataAccessor));
    }

    public function testRemovePermissionForUserOnlyRemovesThatUsersPermission(): void
    {
        $distribution = self::distribution();
        $userToRemove = new User(null);
        $otherUser = new User(null);

        $distribution->addPermissionForUser($userToRemove, PermissionType::edit());
        $otherPermission = $distribution->addPermissionForUser($otherUser, PermissionType::view());

        $distribution->removePermissionForUser($userToRemove);

        self::assertNull($distribution->getPermissionsForUser($userToRemove));
        self::assertSame($otherPermission, $distribution->getPermissionsForUser($otherUser));
        self::assertCount(1, $distribution->getPermissions());
    }

    private static function distribution(): Distribution
    {
        return new Distribution('my-distribution', new Dataset('my-dataset'));
    }
}
