<?php
declare(strict_types=1);

namespace App\CommandHandler\Security;

use App\Command\Security\GetCastorServersCommand;
use App\Security\CastorServer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCastorServersCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return CastorServer[]
     */
    public function __invoke(GetCastorServersCommand $command): array
    {
        /** @var CastorServer[] $servers */
        $servers = $this->em->getRepository(CastorServer::class)->findAll();

        return $servers;
    }
}
