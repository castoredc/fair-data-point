<?php
declare(strict_types=1);

namespace App\Tests\unit\Security\Authorization\Voter;

use App\Entity\DataSpecification\DataModel\DataModel;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Enum\PermissionType;
use App\Repository\DataSpecification\MetadataModel\MetadataModelRepository;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use stdClass;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class DataSpecificationVoterTest extends TestCase
{
    public function testAbstainsForUnsupportedAttribute(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new DataModel('Model', null), ['delete'])
        );
    }

    public function testAbstainsForUnsupportedSubject(): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_ABSTAIN,
            $voter->vote($this->createToken(null), new stdClass(), [DataSpecificationVoter::VIEW])
        );
    }

    #[DataProvider('providePublicSpecificationVotes')]
    public function testAnonymousUserOnPublicDataSpecification(string $attribute, int $expected): void
    {
        $dataModel = new DataModel('Model', null);
        $dataModel->setIsPublic(true);

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken(null), $dataModel, [$attribute]));
    }

    /** @return array<string, array{string, int}> */
    public static function providePublicSpecificationVotes(): array
    {
        return [
            'view granted' => [DataSpecificationVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'use granted' => [DataSpecificationVoter::USE, VoterInterface::ACCESS_GRANTED],
            'add denied' => [DataSpecificationVoter::ADD, VoterInterface::ACCESS_DENIED],
            'edit denied' => [DataSpecificationVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'manage denied' => [DataSpecificationVoter::MANAGE, VoterInterface::ACCESS_DENIED],
        ];
    }

    #[DataProvider('provideAttributes')]
    public function testAnonymousUserIsDeniedEverythingOnPrivateDataSpecification(string $attribute): void
    {
        $voter = $this->createVoter(false);

        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken(null), new DataModel('Model', null), [$attribute])
        );
    }

    #[DataProvider('provideAttributes')]
    public function testAdminUserIsGrantedEverythingOnPrivateDataSpecification(string $attribute): void
    {
        $voter = $this->createVoter(true);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken(new User(null)), new DataModel('Model', null), [$attribute])
        );
    }

    /** @return array<string, array{string}> */
    public static function provideAttributes(): array
    {
        return [
            'use' => [DataSpecificationVoter::USE],
            'view' => [DataSpecificationVoter::VIEW],
            'add' => [DataSpecificationVoter::ADD],
            'edit' => [DataSpecificationVoter::EDIT],
            'manage' => [DataSpecificationVoter::MANAGE],
        ];
    }

    #[DataProvider('providePermissionMatrix')]
    public function testUserWithPermissionOnPrivateDataSpecification(
        ?string $permissionType,
        string $attribute,
        int $expected,
    ): void {
        $user = new User(null);
        $dataModel = new DataModel('Model', null);

        if ($permissionType !== null) {
            $dataModel->addPermissionForUser($user, PermissionType::fromString($permissionType));
        }

        $voter = $this->createVoter(false);

        self::assertSame($expected, $voter->vote($this->createToken($user), $dataModel, [$attribute]));
    }

    /** @return array<string, array{string|null, string, int}> */
    public static function providePermissionMatrix(): array
    {
        return [
            'no permission: view denied' => [null, DataSpecificationVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'no permission: use denied' => [null, DataSpecificationVoter::USE, VoterInterface::ACCESS_DENIED],
            'no permission: add denied' => [null, DataSpecificationVoter::ADD, VoterInterface::ACCESS_DENIED],
            'no permission: edit denied' => [null, DataSpecificationVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'no permission: manage denied' => [null, DataSpecificationVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'view permission: view granted' => ['view', DataSpecificationVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'view permission: use granted' => ['view', DataSpecificationVoter::USE, VoterInterface::ACCESS_GRANTED],
            'view permission: add denied' => ['view', DataSpecificationVoter::ADD, VoterInterface::ACCESS_DENIED],
            'view permission: edit denied' => ['view', DataSpecificationVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'view permission: manage denied' => ['view', DataSpecificationVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'edit permission: view granted' => ['edit', DataSpecificationVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'edit permission: use granted' => ['edit', DataSpecificationVoter::USE, VoterInterface::ACCESS_GRANTED],
            'edit permission: add denied' => ['edit', DataSpecificationVoter::ADD, VoterInterface::ACCESS_DENIED],
            'edit permission: edit granted' => ['edit', DataSpecificationVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'edit permission: manage denied' => ['edit', DataSpecificationVoter::MANAGE, VoterInterface::ACCESS_DENIED],
            'manage permission: view granted' => ['manage', DataSpecificationVoter::VIEW, VoterInterface::ACCESS_GRANTED],
            'manage permission: use granted' => ['manage', DataSpecificationVoter::USE, VoterInterface::ACCESS_GRANTED],
            'manage permission: add denied' => ['manage', DataSpecificationVoter::ADD, VoterInterface::ACCESS_DENIED],
            'manage permission: edit granted' => ['manage', DataSpecificationVoter::EDIT, VoterInterface::ACCESS_GRANTED],
            'manage permission: manage granted' => ['manage', DataSpecificationVoter::MANAGE, VoterInterface::ACCESS_GRANTED],
        ];
    }

    #[DataProvider('provideInheritedUseVotes')]
    public function testMetadataModelInUseByEntitiesUserHasPermissionsTo(
        bool $isInUse,
        string $attribute,
        int $expected,
    ): void {
        $user = new User(null);
        $metadataModel = new MetadataModel('Model', null);

        $voter = $this->createVoter(false, $isInUse);

        self::assertSame($expected, $voter->vote($this->createToken($user), $metadataModel, [$attribute]));
    }

    /** @return array<string, array{bool, string, int}> */
    public static function provideInheritedUseVotes(): array
    {
        return [
            'in use: use granted without explicit permission' => [true, DataSpecificationVoter::USE, VoterInterface::ACCESS_GRANTED],
            'in use: view still denied without explicit permission' => [true, DataSpecificationVoter::VIEW, VoterInterface::ACCESS_DENIED],
            'in use: edit still denied without explicit permission' => [true, DataSpecificationVoter::EDIT, VoterInterface::ACCESS_DENIED],
            'not in use: use denied without explicit permission' => [false, DataSpecificationVoter::USE, VoterInterface::ACCESS_DENIED],
        ];
    }

    public function testMetadataModelPermissionMatrixStillApplies(): void
    {
        $user = new User(null);
        $metadataModel = new MetadataModel('Model', null);
        $metadataModel->addPermissionForUser($user, PermissionType::view());

        $voter = $this->createVoter(false, false);

        self::assertSame(
            VoterInterface::ACCESS_GRANTED,
            $voter->vote($this->createToken($user), $metadataModel, [DataSpecificationVoter::VIEW])
        );
        self::assertSame(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($this->createToken($user), $metadataModel, [DataSpecificationVoter::EDIT])
        );
    }

    private function createVoter(bool $isAdmin, bool $metadataModelIsInUse = false): DataSpecificationVoter
    {
        $security = $this->createMock(Security::class);
        $security->method('isGranted')->willReturnCallback(
            static fn (mixed $attribute): bool => $attribute === 'ROLE_ADMIN' && $isAdmin
        );

        $repository = $this->createMock(MetadataModelRepository::class);
        $repository->method('isInUseByEntitiesUserHasPermissionsTo')->willReturn($metadataModelIsInUse);

        $em = $this->createMock(EntityManagerInterface::class);
        $em->method('getRepository')->with(MetadataModel::class)->willReturn($repository);

        return new DataSpecificationVoter($security, $em);
    }

    private function createToken(?User $user): TokenInterface
    {
        $token = $this->createMock(TokenInterface::class);
        $token->method('getUser')->willReturn($user);

        return $token;
    }
}
