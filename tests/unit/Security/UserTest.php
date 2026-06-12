<?php
declare(strict_types=1);

namespace App\Tests\unit\Security;

use App\Security\Providers\Castor\CastorUser;
use App\Security\Providers\Orcid\OrcidUser;
use App\Security\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class UserTest extends TestCase
{
    public function testPlainUserOnlyHasUserRole(): void
    {
        $user = new User(null);

        self::assertSame(['ROLE_USER'], $user->getRoles());
        self::assertFalse($user->isAdmin());
    }

    public function testUserWithCastorUserHasCastorUserRole(): void
    {
        $user = new User(null);
        $user->setCastorUser(self::castorUser('jane.doe@example.com'));

        self::assertSame(['ROLE_USER', 'ROLE_CASTOR_USER'], $user->getRoles());
        self::assertFalse($user->isAdmin());
    }

    public function testCastorUserWithCastorEmailDomainIsAdmin(): void
    {
        $user = new User(null);
        $user->setCastorUser(self::castorUser('jane.doe@castoredc.com'));

        self::assertSame(['ROLE_USER', 'ROLE_CASTOR_USER', 'ROLE_ADMIN'], $user->getRoles());
        self::assertTrue($user->isAdmin());
    }

    public function testAdminDomainMatchIsCaseInsensitive(): void
    {
        $user = new User(null);
        $user->setCastorUser(self::castorUser('Jane.Doe@CastorEDC.com'));

        self::assertSame(['ROLE_USER', 'ROLE_CASTOR_USER', 'ROLE_ADMIN'], $user->getRoles());
        self::assertTrue($user->isAdmin());
    }

    #[DataProvider('provideNonAdminEmailAddresses')]
    public function testCastorUserWithOtherEmailAddressIsNotAdmin(string $emailAddress): void
    {
        $user = new User(null);
        $user->setCastorUser(self::castorUser($emailAddress));

        self::assertSame(['ROLE_USER', 'ROLE_CASTOR_USER'], $user->getRoles());
        self::assertFalse($user->isAdmin());
    }

    /** @return array<string, array<string>> */
    public static function provideNonAdminEmailAddresses(): array
    {
        return [
            'different domain' => ['jane.doe@example.com'],
            'subdomain of admin domain' => ['jane.doe@fake.castoredc.com'],
            'domain with admin domain as suffix' => ['jane.doe@notcastoredc.com'],
            'admin domain in local part only' => ['castoredc.com@example.com'],
            'no at-sign at all' => ['castoredc.com'],
        ];
    }

    public function testUserWithOrcidHasOrcidUserRole(): void
    {
        $user = new User(null);
        $user->setOrcid(new OrcidUser('0000-0001-2345-6789', 'Jane Doe'));

        self::assertSame(['ROLE_USER', 'ROLE_ORCID_USER'], $user->getRoles());
        self::assertFalse($user->isAdmin());
    }

    public function testUserWithCastorUserAndOrcidHasAllApplicableRoles(): void
    {
        $user = new User(null);
        $user->setCastorUser(self::castorUser('jane.doe@castoredc.com'));
        $user->setOrcid(new OrcidUser('0000-0001-2345-6789', 'Jane Doe'));

        self::assertSame(
            ['ROLE_USER', 'ROLE_CASTOR_USER', 'ROLE_ADMIN', 'ROLE_ORCID_USER'],
            $user->getRoles()
        );
        self::assertTrue($user->isAdmin());
    }

    public function testUserWithNonAdminCastorUserAndOrcidHasBothProviderRoles(): void
    {
        $user = new User(null);
        $user->setCastorUser(self::castorUser('jane.doe@example.com'));
        $user->setOrcid(new OrcidUser('0000-0001-2345-6789', 'Jane Doe'));

        self::assertSame(['ROLE_USER', 'ROLE_CASTOR_USER', 'ROLE_ORCID_USER'], $user->getRoles());
        self::assertFalse($user->isAdmin());
    }

    private static function castorUser(string $emailAddress): CastorUser
    {
        return new CastorUser('castor-user-id', 'Jane', null, 'Doe', $emailAddress);
    }
}
