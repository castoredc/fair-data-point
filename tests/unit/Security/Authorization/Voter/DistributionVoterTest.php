<?php
declare(strict_types=1);

namespace App\Tests\unit\Security\Authorization\Voter;

use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Security\Authorization\Voter\DistributionContentsVoter;
use App\Security\Authorization\Voter\DistributionVoter;
use App\Security\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class DistributionVoterTest extends TestCase
{
    public function testAbstainsForUnsupportedAttribute(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), $this->createDistribution(), ['add'])
        );
    }

    public function testAbstainsForUnsupportedSubject(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new stdClass(), [DistributionVoter::VIEW])
        );
    }

    #[DataProvider('provideAnonymousVotes')]
    public function testAnonymousUser(bool $isPublished, string $attribute, int $expected): void
    {
        $distribution = $this->createDistribution();
        $distribution->setIsPublished($isPublished);

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken(null), $distribution, [$attribute]));
    }

    /** @return array<string, array{bool, string, int}> */
    public static function provideAnonymousVotes(): array
    {
        return [
            'published: view granted' => [true, DistributionVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'published: edit denied' => [true, DistributionVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'published: manage denied' => [true, DistributionVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'unpublished: view denied' => [false, DistributionVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'unpublished: edit denied' => [false, DistributionVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'unpublished: manage denied' => [false, DistributionVoter::MANAGE, VoterInterface::ACCESS_DENIED],
        ];
    }

    #[DataProvider('provideAdminAttributes')]
    public function testAdminUserIsGrantedViewEditManageOnUnpublishedDistribution(string $attribute): void
    {
        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(new User(null)), $this->createDistribution(), [$attribute])
        );
    }

    /** @return array<string, array{string}> */
    public static function provideAdminAttributes(): array
    {
        return [
            'view' => [DistributionVoter::VIEW],
            'edit' => [DistributionVoter::EDIT],
            'manage' => [DistributionVoter::MANAGE],
        ];
    }

    public function testAccessDataIsDeniedWhenDistributionHasNoContentsEvenForAdmins(): void
    {
        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken(new User(null)), $this->createDistribution(), [DistributionVoter::ACCESS_DATA])
        );
    }

    #[DataProvider('provideDelegatedAccessDataVotes')]
    public function testAccessDataIsDelegatedToDistributionContentsVoter(bool $delegatedDecision, int $expected): void
    {
        $distribution = $this->createDistribution();
        $contents = new RDFDistribution($distribution);
        $distribution->setContents($contents);

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static function (mixed $attribute, mixed $subject = null) use ($contents, $delegatedDecision): bool {
                return $attribute === DistributionContentsVoter::ACCESS_DATA && $subject === $contents && $delegatedDecision;
            }
        );

        $voter = new DistributionVoter($security);

        self::assertSame(
            $expected,
            $voter->vote($this->createToken(null), $distribution, [DistributionVoter::ACCESS_DATA])
        );
    }

    /** @return array<string, array{bool, int}> */
    public static function provideDelegatedAccessDataVotes(): array
    {
        return [
            'contents voter grants' => [true, VoterInterface::ACCESS_GRANTED],
            'contents voter denies' => [false, VoterInterface::ACCESS_DENIED],
        ];
    }

    #[DataProvider('providePermissionMatrix')]
    public function testUserWithPermissionOnUnpublishedDistribution(
        ?string $permissionType,
        string $attribute,
        int $expected,
    ): void {
        $user = new User(null);
        $distribution = $this->createDistribution();

        if ($permissionType !== null) {
            $distribution->addPermissionForUser($user, PermissionType::fromString($permissionType));
        }

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken($user), $distribution, [$attribute]));
    }

    /** @return array<string, array{string|null, string, int}> */
    public static function providePermissionMatrix(): array
    {
        return [
            'no permission: view denied' => [null, DistributionVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'no permission: edit denied' => [null, DistributionVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'no permission: manage denied' => [null, DistributionVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'view permission: view granted' => ['view', DistributionVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'view permission: edit denied' => ['view', DistributionVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'view permission: manage denied' => ['view', DistributionVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'edit permission: view granted' => ['edit', DistributionVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'edit permission: edit granted' => ['edit', DistributionVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'edit permission: manage denied' => ['edit', DistributionVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'manage permission: view granted' => ['manage', DistributionVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'manage permission: edit granted' => ['manage', DistributionVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'manage permission: manage granted' => ['manage', DistributionVoter::MANAGE, VoterInterface::ACCESS_GRANTED],
        ];
    }

    private function createDistribution(): Distribution
    {
        return new Distribution('distribution', new Dataset('dataset'));
    }

    private function createVoter(bool $isAdmin): DistributionVoter
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static fn (mixed $attribute): bool => $attribute === 'ROLE_ADMIN' && $isAdmin
        );

        return new DistributionVoter($security);
    }

    private function createToken(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
