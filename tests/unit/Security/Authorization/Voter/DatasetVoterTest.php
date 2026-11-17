<?php
declare(strict_types=1);

namespace App\Tests\unit\Security\Authorization\Voter;

use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Security\Authorization\Voter\DatasetVoter;
use App\Security\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class DatasetVoterTest extends TestCase
{
    public function testAbstainsForUnsupportedAttribute(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new Dataset('dataset'), ['add'])
        );
    }

    public function testAbstainsForUnsupportedSubject(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new stdClass(), [DatasetVoter::VIEW])
        );
    }

    #[DataProvider('provideAnonymousVotes')]
    public function testAnonymousUser(bool $isPublished, string $attribute, int $expected): void
    {
        $dataset = new Dataset('dataset');
        $dataset->setIsPublished($isPublished);

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken(null), $dataset, [$attribute]));
    }

    /** @return array<string, array{bool, string, int}> */
    public static function provideAnonymousVotes(): array
    {
        return [
            'published: view granted' => [true, DatasetVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'published: edit denied' => [true, DatasetVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'published: manage denied' => [true, DatasetVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'unpublished: view denied' => [false, DatasetVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'unpublished: edit denied' => [false, DatasetVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'unpublished: manage denied' => [false, DatasetVoter::MANAGE, VoterInterface::ACCESS_DENIED],
        ];
    }

    #[DataProvider('provideAttributes')]
    public function testAdminUserIsGrantedEverythingOnUnpublishedDataset(string $attribute): void
    {
        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(new User(null)), new Dataset('dataset'), [$attribute])
        );
    }

    /** @return array<string, array{string}> */
    public static function provideAttributes(): array
    {
        return [
            'view' => [DatasetVoter::VIEW],
            'edit' => [DatasetVoter::EDIT],
            'manage' => [DatasetVoter::MANAGE],
        ];
    }

    #[DataProvider('providePermissionMatrix')]
    public function testUserWithPermissionOnUnpublishedDataset(
        ?string $permissionType,
        string $attribute,
        int $expected,
    ): void {
        $user = new User(null);
        $dataset = new Dataset('dataset');

        if ($permissionType !== null) {
            $dataset->addPermissionForUser($user, PermissionType::fromString($permissionType));
        }

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken($user), $dataset, [$attribute]));
    }

    /** @return array<string, array{string|null, string, int}> */
    public static function providePermissionMatrix(): array
    {
        return [
            'no permission: view denied' => [null, DatasetVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'no permission: edit denied' => [null, DatasetVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'no permission: manage denied' => [null, DatasetVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'view permission: view granted' => ['view', DatasetVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'view permission: edit denied' => ['view', DatasetVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'view permission: manage denied' => ['view', DatasetVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'edit permission: view granted' => ['edit', DatasetVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'edit permission: edit granted' => ['edit', DatasetVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'edit permission: manage denied' => ['edit', DatasetVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'manage permission: view granted' => ['manage', DatasetVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'manage permission: edit granted' => ['manage', DatasetVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'manage permission: manage granted' => ['manage', DatasetVoter::MANAGE, VoterInterface::ACCESS_GRANTED],
        ];
    }

    public function testPermissionOfOtherUserDoesNotGrantAccess(): void
    {
        $otherUser = new User(null);
        $user = new User(null);

        $dataset = new Dataset('dataset');
        $dataset->addPermissionForUser($otherUser, PermissionType::manage());

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $dataset, [DatasetVoter::EDIT])
        );
    }

    private function createVoter(bool $isAdmin): DatasetVoter
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static fn (mixed $attribute): bool => $attribute === 'ROLE_ADMIN' && $isAdmin
        );

        return new DatasetVoter($security);
    }

    private function createToken(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
