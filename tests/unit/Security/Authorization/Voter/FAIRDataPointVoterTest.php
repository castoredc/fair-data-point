<?php
declare(strict_types=1);

namespace App\Tests\unit\Security\Authorization\Voter;

use App\Entity\FAIRData\FAIRDataPoint;
use App\Security\Authorization\Voter\FAIRDataPointVoter;
use App\Security\User;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class FAIRDataPointVoterTest extends TestCase
{
    public function testAbstainsForUnsupportedAttribute(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new FAIRDataPoint(), ['manage'])
        );
    }

    public function testAbstainsForUnsupportedSubject(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new stdClass(), [FAIRDataPointVoter::VIEW])
        );
    }

    public function testViewIsGrantedForAnonymousUser(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(null), new FAIRDataPoint(), [FAIRDataPointVoter::VIEW])
        );
    }

    public function testViewIsGrantedForAuthenticatedUser(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(new User(null)), new FAIRDataPoint(), [FAIRDataPointVoter::VIEW])
        );
    }

    public function testEditIsDeniedForAnonymousUser(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken(null), new FAIRDataPoint(), [FAIRDataPointVoter::EDIT])
        );
    }

    public function testEditIsDeniedForNonAdminUser(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken(new User(null)), new FAIRDataPoint(), [FAIRDataPointVoter::EDIT])
        );
    }

    public function testEditIsGrantedForAdminUser(): void
    {
        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(new User(null)), new FAIRDataPoint(), [FAIRDataPointVoter::EDIT])
        );
    }

    private function createVoter(bool $isAdmin): FAIRDataPointVoter
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static fn (mixed $attribute): bool => $attribute === 'ROLE_ADMIN' && $isAdmin
        );

        return new FAIRDataPointVoter($security);
    }

    private function createToken(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
