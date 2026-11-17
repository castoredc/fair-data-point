<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Study;

use App\Command\Study\CreateStudyCommand;
use App\CommandHandler\Study\CreateStudyCommandHandler;
use App\Entity\Castor\CastorStudy;
use App\Entity\Enum\StudySource;
use App\Entity\Study;
use App\Exception\NoAccessPermission;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\StudyAlreadyExists;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Repository\CastorServerRepository;
use App\Repository\StudyRepository;
use App\Security\CastorServer;
use App\Security\Providers\Castor\CastorUser;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class CreateStudyCommandHandlerTest extends TestCase
{
    public function testThrowsWhenUserIsNotAdmin(): void
    {
        $this->expectException(NoAccessPermission::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->expects(self::once())
            ->method('isGranted')
            ->with('ROLE_ADMIN')
            ->willReturn(false);

        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $handler = new CreateStudyCommandHandler($em, $security, $apiClient);
        $handler(new CreateStudyCommand(StudySource::castor(), false, 'SOURCE-1', 1, 'My study'));
    }

    public function testThrowsWhenStudyAlreadyExists(): void
    {
        $this->expectException(StudyAlreadyExists::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $studyRepository = $this->createMock(StudyRepository::class);
        $em->expects(self::once())
            ->method('getRepository')
            ->with(Study::class)
            ->willReturn($studyRepository);

        $studyRepository->expects(self::once())
            ->method('studyExists')
            ->willReturn(true);

        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $handler = new CreateStudyCommandHandler($em, $security, $apiClient);
        $handler(new CreateStudyCommand(StudySource::castor(), false, 'SOURCE-1', 1, 'My study'));
    }

    public function testThrowsWhenUserIsNotACastorUser(): void
    {
        $this->expectException(UserNotACastorUser::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $studyRepository = $this->createMock(StudyRepository::class);
        $serverRepository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')
            ->willReturnMap([
                [Study::class, $studyRepository],
                [CastorServer::class, $serverRepository],
            ]);

        $studyRepository->method('studyExists')->willReturn(false);

        // A user without a Castor user.
        $security->method('getUser')->willReturn(new User(null));

        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $handler = new CreateStudyCommandHandler($em, $security, $apiClient);
        $handler(new CreateStudyCommand(StudySource::castor(), false, 'SOURCE-1', 1, 'My study'));
    }

    public function testCanCreateManuallyEnteredStudy(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $studyRepository = $this->createMock(StudyRepository::class);
        $serverRepository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')
            ->willReturnMap([
                [Study::class, $studyRepository],
                [CastorServer::class, $serverRepository],
            ]);

        $studyRepository->method('studyExists')->willReturn(false);

        $security->method('getUser')->willReturn($this->getCastorUserUser([]));

        $server = $this->getCastorServer();
        $serverRepository->expects(self::once())
            ->method('find')
            ->with(7)
            ->willReturn($server);
        // The API should never be hit for a manually entered study.
        $apiClient->expects(self::never())->method('getStudy');

        $persisted = null;
        $em->expects(self::once())
            ->method('persist')
            ->willReturnCallback(static function ($study) use (&$persisted): void {
                $persisted = $study;
            });
        $em->expects(self::once())->method('flush');

        $handler = new CreateStudyCommandHandler($em, $security, $apiClient);
        $study = $handler(new CreateStudyCommand(StudySource::castor(), true, 'SOURCE-1', 7, 'My study'));

        self::assertInstanceOf(CastorStudy::class, $study);
        self::assertSame($study, $persisted);
        self::assertTrue($study->isEnteredManually());
        self::assertSame('SOURCE-1', $study->getSourceId());
        self::assertSame($server, $study->getServer());
    }

    public function testCanCreateStudyFetchedFromApi(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $studyRepository = $this->createMock(StudyRepository::class);
        $serverRepository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')
            ->willReturnMap([
                [Study::class, $studyRepository],
                [CastorServer::class, $serverRepository],
            ]);

        $studyRepository->method('studyExists')->willReturn(false);

        $security->method('getUser')->willReturn($this->getCastorUserUser(['SOURCE-1'], 'https://data.castoredc.com'));

        $apiStudy = new CastorStudy('SOURCE-1', 'Fetched study', 'fetched-study');
        $apiClient->expects(self::once())->method('setUser');
        $apiClient->expects(self::once())
            ->method('getStudy')
            ->with('SOURCE-1')
            ->willReturn($apiStudy);

        $server = $this->getCastorServer();
        $serverRepository->expects(self::once())
            ->method('findServerByUrl')
            ->with('https://data.castoredc.com')
            ->willReturn($server);

        $em->expects(self::once())->method('persist')->with($apiStudy);
        $em->expects(self::once())->method('flush');

        $handler = new CreateStudyCommandHandler($em, $security, $apiClient);
        $study = $handler(new CreateStudyCommand(StudySource::castor(), false, 'SOURCE-1', null, 'Fetched study'));

        self::assertSame($apiStudy, $study);
        self::assertSame($server, $study->getServer());
    }

    public function testThrowsWhenUserHasNoAccessToStudy(): void
    {
        $this->expectException(NoAccessPermissionToStudy::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('isGranted')->with('ROLE_ADMIN')->willReturn(true);

        $studyRepository = $this->createMock(StudyRepository::class);
        $serverRepository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')
            ->willReturnMap([
                [Study::class, $studyRepository],
                [CastorServer::class, $serverRepository],
            ]);

        $studyRepository->method('studyExists')->willReturn(false);

        // User's Castor user does not have access to the requested study id.
        $security->method('getUser')->willReturn($this->getCastorUserUser(['OTHER-STUDY']));

        $apiClient->expects(self::never())->method('getStudy');
        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $handler = new CreateStudyCommandHandler($em, $security, $apiClient);
        // Not manually entered, so it goes down the API path which checks study access.
        $handler(new CreateStudyCommand(StudySource::castor(), false, 'SOURCE-1', null, 'My study'));
    }

    /** @param string[] $studies */
    private function getCastorUserUser(array $studies, ?string $server = null): User
    {
        $castorUser = new CastorUser('castor-1', 'Jane', null, 'Doe', 'jane@example.com');
        $castorUser->setStudies($studies);

        if ($server !== null) {
            $castorUser->setServer($server);
        }

        $user = new User(null);
        $user->setCastorUser($castorUser);

        return $user;
    }

    private function getCastorServer(): CastorServer
    {
        return CastorServer::defaultServer('https://data.castoredc.com', 'data.castoredc.com', 'nl');
    }
}
