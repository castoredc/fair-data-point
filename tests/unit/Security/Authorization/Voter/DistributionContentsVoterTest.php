<?php
declare(strict_types=1);

namespace App\Tests\unit\Security\Authorization\Voter;

use App\Entity\Castor\CastorStudy;
use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Data\DistributionContents\RDFDistribution;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Security\Authorization\Voter\DistributionContentsVoter;
use App\Security\Authorization\Voter\DistributionVoter;
use App\Security\Providers\Castor\CastorUser;
use App\Security\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class DistributionContentsVoterTest extends TestCase
{
    private const STUDY_SOURCE_ID = 'study-source-1';

    public function testAbstainsForUnsupportedAttribute(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), $this->createContents(self::STUDY_SOURCE_ID), ['view'])
        );
    }

    public function testAbstainsForUnsupportedSubject(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new stdClass(), [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    #[DataProvider('provideDelegatedManageVotes')]
    public function testManageIsDelegatedToDistributionVoter(bool $delegatedDecision, int $expected): void
    {
        $contents = $this->createContents(self::STUDY_SOURCE_ID);
        $distribution = $contents->getDistribution();

        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static function (mixed $attribute, mixed $subject = null) use ($distribution, $delegatedDecision): bool {
                return $attribute === DistributionVoter::MANAGE && $subject === $distribution && $delegatedDecision;
            }
        );

        $voter = new DistributionContentsVoter($security);

        self::assertSame(
            $expected,
            $voter->vote($this->createToken(null), $contents, [DistributionContentsVoter::MANAGE])
        );
    }

    /** @return array<string, array{bool, int}> */
    public static function provideDelegatedManageVotes(): array
    {
        return [
            'distribution voter grants' => [true, VoterInterface::ACCESS_GRANTED],
            'distribution voter denies' => [false, VoterInterface::ACCESS_DENIED],
        ];
    }

    public function testAccessDataIsGrantedForAnonymousUserWhenContentsArePublic(): void
    {
        $contents = $this->createContents(self::STUDY_SOURCE_ID);
        $contents->setIsPublic(true);

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(null), $contents, [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    public function testAccessDataIsDeniedForAnonymousUserWhenContentsAreNotPublic(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken(null), $this->createContents(self::STUDY_SOURCE_ID), [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    public function testAccessDataIsGrantedForAdmin(): void
    {
        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(new User(null)), $this->createContents(self::STUDY_SOURCE_ID), [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    public function testAccessDataIsDeniedForUserWithoutPermission(): void
    {
        $user = $this->createCastorUser(true, [self::STUDY_SOURCE_ID]);

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $this->createContents(self::STUDY_SOURCE_ID), [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    public function testAccessDataIsGrantedForCastorUserWithAccessDataPermissionOnDistribution(): void
    {
        $user = $this->createCastorUser(false, []);
        $contents = $this->createContents(self::STUDY_SOURCE_ID);
        $contents->getDistribution()->addPermissionForUser($user, PermissionType::accessData());

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken($user), $contents, [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    public function testAccessDataIsDeniedWhenStudyHasNoSourceId(): void
    {
        $user = $this->createCastorUser(true, []);
        $contents = $this->createContents(null);
        $contents->getDistribution()->addPermissionForUser($user, PermissionType::accessData());

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $contents, [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    public function testAccessDataIsDeniedForUserWithoutCastorAccount(): void
    {
        $user = new User(null);
        $contents = $this->createContents(self::STUDY_SOURCE_ID);
        $contents->getDistribution()->addPermissionForUser($user, PermissionType::accessData());

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $contents, [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    #[DataProvider('provideStudyAccessVotes')]
    public function testNonAccessDataPermissionFallsBackToSourceStudyAccess(
        bool $isAuthenticated,
        bool $hasStudyAccess,
        int $expected,
    ): void {
        $user = $this->createCastorUser($isAuthenticated, $hasStudyAccess ? [self::STUDY_SOURCE_ID] : []);
        $contents = $this->createContents(self::STUDY_SOURCE_ID);
        $contents->getDistribution()->addPermissionForUser($user, PermissionType::edit());

        $voter = $this->createVoter(false);

        self::assertSame(
            $expected,
            $voter->vote($this->createToken($user), $contents, [DistributionContentsVoter::ACCESS_DATA])
        );
    }

    /** @return array<string, array{bool, bool, int}> */
    public static function provideStudyAccessVotes(): array
    {
        return [
            'authenticated castor user with study access is granted' => [true, true, VoterInterface::ACCESS_GRANTED],
            'authenticated castor user without study access is denied' => [true, false, VoterInterface::ACCESS_DENIED],
            'unauthenticated castor user with study access is denied' => [false, true, VoterInterface::ACCESS_DENIED],
        ];
    }

    private function createContents(?string $studySourceId): DistributionContents
    {
        $study = new CastorStudy($studySourceId, 'Study', 'study');

        $dataset = new Dataset('dataset');
        $dataset->setStudy($study);

        $distribution = new Distribution('distribution', $dataset);

        $contents = new RDFDistribution($distribution);
        $distribution->setContents($contents);

        return $contents;
    }

    /** @param string[] $studies */
    private function createCastorUser(bool $isAuthenticated, array $studies): User
    {
        $castorUser = new CastorUser('castor-user-1', 'Test', null, 'User', 'test@example.com');
        $castorUser->setStudies($studies);

        if ($isAuthenticated) {
            $castorUser->setToken('token');
        }

        $user = new User(null);
        $user->setCastorUser($castorUser);

        return $user;
    }

    private function createVoter(bool $isAdmin): DistributionContentsVoter
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static fn (mixed $attribute): bool => $attribute === 'ROLE_ADMIN' && $isAdmin
        );

        return new DistributionContentsVoter($security);
    }

    private function createToken(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
