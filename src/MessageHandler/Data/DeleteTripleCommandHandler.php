<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Message\Data\DeleteTripleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteTripleCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteTripleCommand $command): void
    {
        $triple = $command->getTriple();

        $this->em->remove($triple);

        $this->em->flush();
    }
}
