<?php
declare(strict_types=1);

namespace App\Tests\unit\Security\Authorization\Voter;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Catalog;
use App\Security\Authorization\Voter\CatalogVoter;
use App\Security\Providers\Castor\CastorUser;
use App\Security\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class CatalogVoterTest extends TestCase
{
    public function testAbstainsForUnsupportedAttribute(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new Catalog('catalog'), ['delete'])
        );
    }

    public function testAbstainsForUnsupportedSubject(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new stdClass(), [CatalogVoter::VIEW])
        );
    }

    #[DataProvider('provideAnonymousVotes')]
    public function testAnonymousUser(string $attribute, int $expected): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            $expected,
            $voter->vote($this->createToken(null), new Catalog('catalog'), [$attribute])
        );
    }

    /** @return array<string, array{string, int}> */
    public static function provideAnonymousVotes(): array
    {
        return [
            'view is granted because catalogs are always public' => [CatalogVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'add is denied' => [CatalogVoter::ADD, VoterInterface::ACCESS_DENIED],
            'edit is denied' => [CatalogVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'manage is denied' => [CatalogVoter::MANAGE, VoterInterface::ACCESS_DENIED],
        ];
    }

    #[DataProvider('provideAttributes')]
    public function testAdminUserIsGrantedEverything(string $attribute): void
    {
        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(new User(null)), new Catalog('catalog'), [$attribute])
        );
    }

    /** @return array<string, array{string}> */
    public static function provideAttributes(): array
    {
        return [
            'view' => [CatalogVoter::VIEW],
            'add' => [CatalogVoter::ADD],
            'edit' => [CatalogVoter::EDIT],
            'manage' => [CatalogVoter::MANAGE],
        ];
    }

    #[DataProvider('providePermissionMatrix')]
    public function testCastorUserWithPermission(
        ?string $permissionType,
        bool $acceptSubmissions,
        string $attribute,
        int $expected,
    ): void {
        $user = $this->createCastorUser();

        $catalog = new Catalog('catalog');
        $catalog->setAcceptSubmissions($acceptSubmissions);

        if ($permissionType !== null) {
            $catalog->addPermissionForUser($user, PermissionType::fromString($permissionType));
        }

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken($user), $catalog, [$attribute]));
    }

    /** @return array<string, array{string|null, bool, string, int}> */
    public static function providePermissionMatrix(): array
    {
        return [
            'no permission: view granted (public)' => [null, false, CatalogVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'no permission: add denied' => [null, false, CatalogVoter::ADD, VoterInterface::ACCESS_DENIED],
            'no permission: edit denied' => [null, false, CatalogVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'no permission: manage denied' => [null, false, CatalogVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'view permission: view granted' => ['view', false, CatalogVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'view permission: add denied when not accepting submissions' => ['view', false, CatalogVoter::ADD, VoterInterface::ACCESS_DENIED],
            'view permission: add granted when accepting submissions' => ['view', true, CatalogVoter::ADD, VoterInterface::ACCESS_GRANTED],
            'view permission: edit denied' => ['view', false, CatalogVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'view permission: edit denied when accepting submissions' => ['view', true, CatalogVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'view permission: manage denied' => ['view', false, CatalogVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'edit permission: view granted' => ['edit', false, CatalogVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'edit permission: add granted' => ['edit', false, CatalogVoter::ADD, VoterInterface::ACCESS_GRANTED],
            'edit permission: edit granted' => ['edit', false, CatalogVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'edit permission: manage denied' => ['edit', false, CatalogVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'manage permission: view granted' => ['manage', false, CatalogVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'manage permission: add granted' => ['manage', false, CatalogVoter::ADD, VoterInterface::ACCESS_GRANTED],
            'manage permission: edit granted' => ['manage', false, CatalogVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'manage permission: manage granted' => ['manage', false, CatalogVoter::MANAGE, VoterInterface::ACCESS_GRANTED],
        ];
    }

    #[DataProvider('provideNonCastorUserVotes')]
    public function testUserWithoutCastorAccountIsDeniedDespiteManagePermission(string $attribute, int $expected): void
    {
        $user = new User(null);

        $catalog = new Catalog('catalog');
        $catalog->addPermissionForUser($user, PermissionType::manage());

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken($user), $catalog, [$attribute]));
    }

    /** @return array<string, array{string, int}> */
    public static function provideNonCastorUserVotes(): array
    {
        return [
            'view is granted because catalogs are always public' => [CatalogVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'add is denied' => [CatalogVoter::ADD, VoterInterface::ACCESS_DENIED],
            'edit is denied' => [CatalogVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'manage is denied' => [CatalogVoter::MANAGE, VoterInterface::ACCESS_DENIED],
        ];
    }

    private function createVoter(bool $isAdmin): CatalogVoter
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static fn (mixed $attribute): bool => $attribute === 'ROLE_ADMIN' && $isAdmin
        );

        return new CatalogVoter($security);
    }

    private function createToken(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }

    private function createCastorUser(): User
    {
        $user = new User(null);
        $user->setCastorUser(new CastorUser('castor-user-1', 'Test', null, 'User', 'test@example.com'));

        return $user;
    }
}
