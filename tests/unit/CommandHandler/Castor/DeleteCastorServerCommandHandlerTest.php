<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Castor;

use App\Command\Castor\DeleteCastorServerCommand;
use App\CommandHandler\Castor\DeleteCastorServerCommandHandler;
use App\Exception\Castor\CastorServerNotFound;
use App\Repository\CastorServerRepository;
use App\Security\CastorServer;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class DeleteCastorServerCommandHandlerTest extends TestCase
{
    public function testCanSuccessfullyDeleteCastorServer(): void
    {
        $idToRemove = 4;

        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(CastorServerRepository::class);
        $em->expects(self::once())
            ->method('getRepository')
            ->with(CastorServer::class)
            ->willReturn($repository);
        $em->expects(self::once())
            ->method('flush');

        $castorServer = $this->getCastorServer();
        $repository->expects(self::once())->method('find')->with($idToRemove)->willReturn($castorServer);

        $handler = new DeleteCastorServerCommandHandler($em);
        $handler(new DeleteCastorServerCommand($idToRemove));
    }

    public function testWillThrowIfServerDoesntExist(): void
    {
        $this->expectException(CastorServerNotFound::class);
        $idToRemove = 4;

        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(CastorServerRepository::class);
        $em->expects(self::once())
            ->method('getRepository')
            ->with(CastorServer::class)
            ->willReturn($repository);
        $em->expects(self::never())
            ->method('flush');

        $repository->expects(self::once())->method('find')->with($idToRemove)->willReturn(null);

        $handler = new DeleteCastorServerCommandHandler($em);
        $handler(new DeleteCastorServerCommand($idToRemove));
    }

    private function getCastorServer(): CastorServer
    {
        return CastorServer::defaultServer('https://data.castoredc.com', 'data.castoredc.com', 'nl');
    }
}
