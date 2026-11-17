<?php
declare(strict_types=1);

namespace App\Tests\unit\CommandHandler\Castor;

use App\Command\Castor\UpdateCastorServerCommand;
use App\CommandHandler\Castor\UpdateCastorServerCommandHandler;
use App\Repository\CastorServerRepository;
use App\Security\CastorServer;
use App\Service\EncryptionService;
use App\Tests\Helper\DoctrineEntityIdSetter;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;

final class UpdateCastorServerCommandHandlerTest extends TestCase
{
    use DoctrineEntityIdSetter;

    private const KEY = '0000000000000000000000000000000000000000000000000000000000000000';

    public function testCreatesNewNonDefaultServer(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')->with(CastorServer::class)->willReturn($repository);

        $repository->expects(self::never())->method('find');
        // No id => create flow; non-default => findAll never called.
        $repository->expects(self::never())->method('findAll');

        $em->expects(self::once())->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = new UpdateCastorServerCommandHandler($em, new EncryptionService(self::KEY));
        $server = $handler($this->command(null, false));

        self::assertFalse($server->isDefault());
        self::assertSame('https://example.castoredc.com', $server->getUrl()->getValue());
        self::assertNotNull($server->getClientIdCiphertext());
        self::assertNotNull($server->getClientSecretCiphertext());
    }

    public function testCreatesNewDefaultServerAndMakesOthersNonDefault(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')->with(CastorServer::class)->willReturn($repository);

        $existing = CastorServer::defaultServer('https://old.castoredc.com', 'Old', 'nl');
        $repository->expects(self::once())->method('findAll')->willReturn([$existing]);

        $em->expects(self::once())->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = new UpdateCastorServerCommandHandler($em, new EncryptionService(self::KEY));
        $server = $handler($this->command(null, true));

        self::assertTrue($server->isDefault());
        // The previously-default server has been demoted.
        self::assertFalse($existing->isDefault());
    }

    public function testUpdatesExistingServer(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')->with(CastorServer::class)->willReturn($repository);

        $existing = CastorServer::nonDefaultServer('https://old.castoredc.com', 'Old', 'be');
        $this->setEntityId($existing, 3);

        $other = CastorServer::defaultServer('https://other.castoredc.com', 'Other', 'nl');
        $this->setEntityId($other, 4);

        $repository->expects(self::once())->method('find')->with(3)->willReturn($existing);
        $repository->expects(self::once())->method('findAll')->willReturn([$existing, $other]);

        $em->expects(self::never())->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = new UpdateCastorServerCommandHandler($em, new EncryptionService(self::KEY));
        $server = $handler($this->command(3, true));

        self::assertSame($existing, $server);
        // Updated from command.
        self::assertSame('https://example.castoredc.com', $server->getUrl()->getValue());
        self::assertSame('Example', $server->getName());
        self::assertTrue($server->isDefault());
        // The other server gets demoted because the updated one becomes default.
        self::assertFalse($other->isDefault());
    }

    public function testUpdatingTheOnlyServerForcesItToBeDefault(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')->with(CastorServer::class)->willReturn($repository);

        $existing = CastorServer::nonDefaultServer('https://old.castoredc.com', 'Old', 'be');
        $this->setEntityId($existing, 9);

        $repository->method('find')->with(9)->willReturn($existing);
        // Only one server in total.
        $repository->method('findAll')->willReturn([$existing]);

        $em->expects(self::once())->method('flush');

        $handler = new UpdateCastorServerCommandHandler($em, new EncryptionService(self::KEY));
        // Even though the command says non-default, the single-server invariant forces default.
        $server = $handler($this->command(9, false));

        self::assertTrue($server->isDefault());
    }

    public function testUpdatingNonExistentIdFallsThroughToCreate(): void
    {
        $em = $this->createMock(EntityManagerInterface::class);
        $repository = $this->createMock(CastorServerRepository::class);
        $em->method('getRepository')->with(CastorServer::class)->willReturn($repository);

        // Id provided but not found => create flow.
        $repository->expects(self::once())->method('find')->with(99)->willReturn(null);

        $em->expects(self::once())->method('persist');
        $em->expects(self::once())->method('flush');

        $handler = new UpdateCastorServerCommandHandler($em, new EncryptionService(self::KEY));
        $server = $handler($this->command(99, false));

        self::assertFalse($server->isDefault());
    }

    private function command(?int $id, bool $default): UpdateCastorServerCommand
    {
        return new UpdateCastorServerCommand(
            $id,
            'Example',
            'https://example.castoredc.com',
            'nl',
            $default,
            'client-id',
            'client-secret',
        );
    }
}
