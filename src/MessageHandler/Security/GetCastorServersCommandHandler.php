<?php

namespace App\MessageHandler\Security;

use App\Entity\Castor\CastorServer;
use App\Message\Security\GetCastorServersCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class GetCastorServersCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return CastorServer[]
     */
    public function __invoke(GetCastorServersCommand $message): array
    {
        $servers = $this->em->getRepository(CastorServer::class)->findAll();

        return $servers;
    }
}
