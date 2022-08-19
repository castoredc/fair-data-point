<?php
declare(strict_types=1);

namespace App\CommandHandler\Castor;

use App\Command\Castor\UpdateCastorServerCommand;
use App\Exception\CouldNotTransformEncryptedStringToJson;
use App\Security\CastorServer;
use App\Service\EncryptionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateCastorServerCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;
    private EncryptionService $encryptionService;

    public function __construct(EntityManagerInterface $em, EncryptionService $encryptionService)
    {
        $this->em = $em;
        $this->encryptionService = $encryptionService;
    }

    /** @throws CouldNotTransformEncryptedStringToJson */
    public function __invoke(UpdateCastorServerCommand $command): CastorServer
    {
        $castorServerRepository = $this->em->getRepository(CastorServer::class);

        $castorServer = null;
        if ($command->getId() !== null) {
            $castorServer = $castorServerRepository->find($command->getId());
        }

        if ($castorServer !== null) {
            $castorServer->updatePropertiesFromCommand($command, $this->encryptionService);

            if ($command->isDefault()) {
                // There can be only one default server, so ensure all other servers are non-default.
                $allServers = $castorServerRepository->findAll();
                foreach ($allServers as $server) {
                    // Don't update the CastorServer that we just set to default.
                    if ($server->getId() === $command->getId()) {
                        continue;
                    }

                    $server->makeNonDefault();
                }
            }
        } else {
            if ($command->isDefault()) {
                $castorServer = CastorServer::defaultServer(
                    $command->getUrl(),
                    $command->getName(),
                    $command->getFlag()
                );

                // There can be only one default server, so ensure all other servers are non-default.
                $allServers = $castorServerRepository->findAll();
                foreach ($allServers as $server) {
                    $server->makeNonDefault();
                }
            } else {
                $castorServer = CastorServer::nonDefaultServer(
                    $command->getUrl(),
                    $command->getName(),
                    $command->getFlag()
                );
            }

            $castorServer->updateClientCredentials(
                $this->encryptionService,
                $command->getClientId(),
                $command->getClientSecret()
            );

            $this->em->persist($castorServer);
        }

        $this->em->flush();

        return $castorServer;
    }
}
