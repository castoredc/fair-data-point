<?php
declare(strict_types=1);

namespace App\CommandHandler\Castor;

use App\Command\Castor\DeleteCastorServerCommand;
use App\Exception\Castor\CastorServerNotFound;
use App\Security\CastorServer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class DeleteCastorServerCommandHandler
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteCastorServerCommand $command): void
    {
        $castorServerRepository = $this->em->getRepository(CastorServer::class);
        $castorServer = $castorServerRepository->find($command->getId());

        if ($castorServer === null) {
            throw new CastorServerNotFound();
        }

        $this->em->remove($castorServer);
        $this->em->flush();
    }
}
