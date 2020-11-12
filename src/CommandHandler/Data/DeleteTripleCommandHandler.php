<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Exception\NoAccessPermission;
use App\Command\Data\DeleteTripleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class DeleteTripleCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(DeleteTripleCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $triple = $command->getTriple();

        $this->em->remove($triple);

        $this->em->flush();
    }
}
