<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Castor;

use App\Command\Castor\ImportStudyCommand;
use App\CommandHandler\Castor\ImportStudyCommandHandler;
use App\Entity\Castor\CastorStudy;
use App\Entity\Study;
use App\Exception\NoAccessPermissionToStudy;
use App\Exception\UserNotACastorUser;
use App\Model\Castor\ApiClient;
use App\Repository\CastorServerRepository;
use App\Repository\StudyRepository;
use App\Security\Authorization\Voter\StudyVoter;
use App\Security\CastorServer;
use App\Security\Providers\Castor\CastorUser;
use App\Security\User;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;

final class ImportStudyCommandHandlerTest extends TestCase
{
    public function testThrowsWhenUserIsNotACastorUser(): void
    {
        $this->expectException(UserNotACastorUser::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('getUser')->willReturn(new User(null));
        $apiClient->expects(self::never())->method('setUser');

        $handler = new ImportStudyCommandHandler($em, $security, $apiClient);
        $handler(new ImportStudyCommand('SOURCE-1'));
    }

    public function testReturnsExistingStudyWhenUserCanEdit(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('getUser')->willReturn($this->getCastorUser());

        $existing = new CastorStudy('SOURCE-1', 'Existing', 'existing');

        $studyRepository = $this->createMock(StudyRepository::class);
        $em->expects(self::once())
            ->method('getRepository')
            ->with(Study::class)
            ->willReturn($studyRepository);

        $studyRepository->expects(self::once())->method('studyExists')->willReturn(true);
        $studyRepository->expects(self::once())
            ->method('findStudyBySourceAndId')
            ->willReturn($existing);

        $security->expects(self::once())
            ->method('isGranted')
            ->with(StudyVoter::EDIT, $existing)
            ->willReturn(true);

        $apiClient->expects(self::never())->method('getStudy');
        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $handler = new ImportStudyCommandHandler($em, $security, $apiClient);
        $study = $handler(new ImportStudyCommand('SOURCE-1'));

        self::assertSame($existing, $study);
    }

    public function testThrowsWhenExistingStudyCannotBeEdited(): void
    {
        $this->expectException(NoAccessPermissionToStudy::class);

        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('getUser')->willReturn($this->getCastorUser());

        $existing = new CastorStudy('SOURCE-1', 'Existing', 'existing');

        $studyRepository = $this->createMock(StudyRepository::class);
        $em->method('getRepository')->with(Study::class)->willReturn($studyRepository);
        $studyRepository->method('studyExists')->willReturn(true);
        $studyRepository->method('findStudyBySourceAndId')->willReturn($existing);

        $security->method('isGranted')->with(StudyVoter::EDIT, $existing)->willReturn(false);

        $em->expects(self::never())->method('persist');
        $em->expects(self::never())->method('flush');

        $handler = new ImportStudyCommandHandler($em, $security, $apiClient);
        $handler(new ImportStudyCommand('SOURCE-1'));
    }

    public function testImportsNewStudyFromApi(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $security = $this->createMock(Security::class);
        $apiClient = $this->createMock(ApiClient::class);

        $security->method('getUser')->willReturn($this->getCastorUser('https://data.castoredc.com'));

        $studyRepository = $this->createMock(StudyRepository::class);
        $serverRepository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')
            ->willReturnMap([
                [Study::class, $studyRepository],
                [CastorServer::class, $serverRepository],
            ]);

        $studyRepository->method('studyExists')->willReturn(false);

        $server = CastorServer::defaultServer('https://data.castoredc.com', 'Data', 'nl');
        $serverRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['url' => 'https://data.castoredc.com'])
            ->willReturn($server);

        $apiStudy = new CastorStudy('SOURCE-1', 'Fetched', 'fetched');
        $apiClient->expects(self::once())->method('setUser');
        $apiClient->expects(self::once())
            ->method('getStudy')
            ->with('SOURCE-1')
            ->willReturn($apiStudy);

        $em->expects(self::once())->method('persist')->with($apiStudy);
        $em->expects(self::once())->method('flush');

        $handler = new ImportStudyCommandHandler($em, $security, $apiClient);
        $study = $handler(new ImportStudyCommand('SOURCE-1'));

        self::assertSame($apiStudy, $study);
        self::assertSame($server, $study->getServer());
    }

    private function getCastorUser(?string $server = null): User
    {
        $castorUser = new CastorUser('castor-1', 'Jane', null, 'Doe', 'jane@example.com');

        if ($server !== null) {
            $castorUser->setServer($server);
        }

        $user = new User(null);
        $user->setCastorUser($castorUser);

        return $user;
    }
}
