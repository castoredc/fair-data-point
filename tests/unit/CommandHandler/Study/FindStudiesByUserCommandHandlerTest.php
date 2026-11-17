<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Study;

use App\Command\Study\FindStudiesByUserCommand;
use App\CommandHandler\Study\FindStudiesByUserCommandHandler;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Repository\StudyRepository;
use App\Security\Providers\Castor\CastorUser;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class FindStudiesByUserCommandHandlerTest extends TestCase
{
    public function testThrowsWhenUserIsNotACastorUser(): void
    {
        $this->expectException(UserNotACastorUser::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $apiClient = $this->createMock(ApiClient::class);

        $apiClient->expects(self::never())->method('getStudies');

        $handler = new FindStudiesByUserCommandHandler($em, $apiClient);
        $handler(new FindStudiesByUserCommand(new User(null), false));
    }

    public function testReturnsDbStudiesWhenNotLoadingFromCastor(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $apiClient = $this->createMock(ApiClient::class);

        $castorStudies = [
            $this->makeStudy('castor-b', 'B Study'),
            $this->makeStudy('castor-a', 'A Study'),
        ];

        $apiClient->expects(self::once())->method('setUser');
        $apiClient->expects(self::once())->method('getStudies')->willReturn($castorStudies);
        $apiClient->expects(self::once())
            ->method('getStudyIds')
            ->with($castorStudies)
            ->willReturn(['castor-b', 'castor-a']);

        $dbStudies = [
            $this->makeStudy('castor-z', 'Zebra'),
            $this->makeStudy('castor-a', 'Apple'),
        ];

        $repository = $this->createMock(StudyRepository::class);
        $em->expects(self::once())
            ->method('getRepository')
            ->with(Study::class)
            ->willReturn($repository);
        $repository->expects(self::once())
            ->method('findBy')
            ->with(['sourceId' => ['castor-b', 'castor-a']])
            ->willReturn($dbStudies);

        $handler = new FindStudiesByUserCommandHandler($em, $apiClient);
        $result = $handler(new FindStudiesByUserCommand($this->getCastorUser(), false));

        // Returns the DB studies, sorted by name.
        self::assertCount(2, $result);
        self::assertSame('Apple', $result[0]->getName());
        self::assertSame('Zebra', $result[1]->getName());
    }

    public function testReturnsAllCastorStudiesWhenLoadingFromCastorWithoutHiding(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $apiClient = $this->createMock(ApiClient::class);

        $castorStudies = [
            $this->makeStudy('castor-b', 'B Study'),
            $this->makeStudy('castor-a', 'A Study'),
        ];

        $apiClient->method('getStudies')->willReturn($castorStudies);
        $apiClient->method('getStudyIds')->willReturn(['castor-b', 'castor-a']);

        $repository = $this->createMock(StudyRepository::class);
        $em->method('getRepository')->with(Study::class)->willReturn($repository);
        $repository->method('findBy')->willReturn([]);

        $handler = new FindStudiesByUserCommandHandler($em, $apiClient);
        $result = $handler(new FindStudiesByUserCommand($this->getCastorUser(), true, false));

        self::assertCount(2, $result);
        self::assertSame('A Study', $result[0]->getName());
        self::assertSame('B Study', $result[1]->getName());
    }

    public function testHidesExistingStudiesWhenRequested(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $apiClient = $this->createMock(ApiClient::class);

        $existing = $this->makeStudy('castor-a', 'A Study');
        $fresh = $this->makeStudy('castor-b', 'B Study');

        // A DB study whose generated id matches the existing castor study's id.
        $dbStudy = $this->makeStudy('castor-a', 'A Study');
        $dbStudy->setId('shared-id');
        $existing->setId('shared-id');

        $castorStudies = [$existing, $fresh];

        $apiClient->method('getStudies')->willReturn($castorStudies);
        $apiClient->method('getStudyIds')->willReturn(['castor-a', 'castor-b']);

        $repository = $this->createMock(StudyRepository::class);
        $em->method('getRepository')->with(Study::class)->willReturn($repository);
        $repository->method('findBy')->willReturn([$dbStudy]);

        $handler = new FindStudiesByUserCommandHandler($em, $apiClient);
        $result = $handler(new FindStudiesByUserCommand($this->getCastorUser(), true, true));

        // The existing study (matching id) is filtered out; only the fresh one remains.
        self::assertCount(1, $result);
        self::assertSame('B Study', $result[0]->getName());
    }

    private function makeStudy(string $sourceId, string $name): CastorStudy
    {
        return new CastorStudy($sourceId, $name, 'slug-' . $sourceId);
    }

    private function getCastorUser(): User
    {
        $castorUser = new CastorUser('castor-1', 'Jane', null, 'Doe', 'jane@example.com');

        $user = new User(null);
        $user->setCastorUser($castorUser);

        return $user;
    }
}
