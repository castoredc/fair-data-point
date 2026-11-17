<?php
declare(strict_types=1);

namespace App\Tests\unit\Security\Authorization\Voter;

use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\StudySource;
use App\Entity\Study;
use App\Security\Authorization\Voter\StudyVoter;
use App\Security\Providers\Castor\CastorUser;
use App\Security\User;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class StudyVoterTest extends TestCase
{
    private const STUDY_SOURCE_ID = 'study-source-1';

    public function testAbstainsForUnsupportedAttribute(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), $this->createCastorStudy(self::STUDY_SOURCE_ID, false), ['manage'])
        );
    }

    public function testAbstainsForUnsupportedSubject(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new stdClass(), [StudyVoter::VIEW])
        );
    }

    #[DataProvider('provideAnonymousVotes')]
    public function testAnonymousUser(bool $isPublished, string $attribute, int $expected): void
    {
        $study = $this->createCastorStudy(self::STUDY_SOURCE_ID, $isPublished);

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken(null), $study, [$attribute]));
    }

    /** @return array<string, array{bool, string, int}> */
    public static function provideAnonymousVotes(): array
    {
        return [
            'published: view granted' => [true, StudyVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'published: edit denied' => [true, StudyVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'published: edit_source_system denied' => [true, StudyVoter::EDIT_SOURCE_SYSTEM, VoterInterface::ACCESS_DENIED],
            'unpublished: view denied' => [false, StudyVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'unpublished: edit denied' => [false, StudyVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'unpublished: edit_source_system denied' => [false, StudyVoter::EDIT_SOURCE_SYSTEM, VoterInterface::ACCESS_DENIED],
        ];
    }

    public function testAdminUserCanViewAndEditUnpublishedStudy(): void
    {
        $study = $this->createCastorStudy(self::STUDY_SOURCE_ID, false);
        $token = $this->createToken(new User(null));

        $voter = $this->createVoter(true);

        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $study, [StudyVoter::VIEW]));
        self::assertSame(VoterInterface::ACCESS_GRANTED, $voter->vote($token, $study, [StudyVoter::EDIT]));
    }

    public function testAdminUserWithoutCastorAccountCannotEditInSourceSystem(): void
    {
        $study = $this->createCastorStudy(self::STUDY_SOURCE_ID, false);
        $token = $this->createToken(new User(null));

        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, $study, [StudyVoter::EDIT_SOURCE_SYSTEM])
        );
    }

    #[DataProvider('provideCastorUserVotes')]
    public function testCastorUserOnUnpublishedCastorStudy(
        bool $isAuthenticated,
        bool $hasStudyAccess,
        string $attribute,
        int $expected,
    ): void {
        $user = $this->createCastorUser($isAuthenticated, $hasStudyAccess ? [self::STUDY_SOURCE_ID] : []);
        $study = $this->createCastorStudy(self::STUDY_SOURCE_ID, false);

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken($user), $study, [$attribute]));
    }

    /** @return array<string, array{bool, bool, string, int}> */
    public static function provideCastorUserVotes(): array
    {
        return [
            'with study access: view granted' => [true, true, StudyVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'with study access: edit granted' => [true, true, StudyVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'with study access: edit_source_system granted' => [true, true, StudyVoter::EDIT_SOURCE_SYSTEM, VoterInterface::ACCESS_GRANTED],
            'without study access: view denied' => [true, false, StudyVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'without study access: edit denied' => [true, false, StudyVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'without study access: edit_source_system denied' => [true, false, StudyVoter::EDIT_SOURCE_SYSTEM, VoterInterface::ACCESS_DENIED],
            'unauthenticated with study access: edit denied' => [false, true, StudyVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'unauthenticated with study access: edit_source_system denied' => [false, true, StudyVoter::EDIT_SOURCE_SYSTEM, VoterInterface::ACCESS_DENIED],
        ];
    }

    public function testUserWithoutCastorAccountCannotEdit(): void
    {
        $study = $this->createCastorStudy(self::STUDY_SOURCE_ID, false);
        $token = $this->createToken(new User(null));

        $voter = $this->createVoter(false);

        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, $study, [StudyVoter::EDIT]));
        self::assertSame(VoterInterface::ACCESS_DENIED, $voter->vote($token, $study, [StudyVoter::EDIT_SOURCE_SYSTEM]));
    }

    public function testCastorUserCannotEditCastorStudyWithoutSourceId(): void
    {
        $user = $this->createCastorUser(true, [self::STUDY_SOURCE_ID]);
        $study = $this->createCastorStudy(null, false);

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $study, [StudyVoter::EDIT])
        );
    }

    public function testCastorUserCannotEditStudyFromOtherSourceSystem(): void
    {
        $user = $this->createCastorUser(true, [self::STUDY_SOURCE_ID]);

        $study = new class (StudySource::other(), self::STUDY_SOURCE_ID, 'Study', 'study') extends Study {
        };

        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $study, [StudyVoter::EDIT])
        );
        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $study, [StudyVoter::EDIT_SOURCE_SYSTEM])
        );
    }

    private function createCastorStudy(?string $sourceId, bool $isPublished): CastorStudy
    {
        $study = new CastorStudy($sourceId, 'Study', 'study');
        $study->setIsPublished($isPublished);

        return $study;
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

    private function createVoter(bool $isAdmin): StudyVoter
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static fn (mixed $attribute): bool => $attribute === 'ROLE_ADMIN' && $isAdmin
        );

        return new StudyVoter($security);
    }

    private function createToken(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
