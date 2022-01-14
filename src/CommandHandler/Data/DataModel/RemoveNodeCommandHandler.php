<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\RemoveNodeCommand;
use App\Exception\NoAccessPermission;
use App\Exception\NodeInUseByTriples;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class RemoveNodeCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    /**
     * @throws NodeInUseByTriples
     * @throws NoAccessPermission
     */
    public function __invoke(RemoveNodeCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $node = $command->getNode();

        if ($node->hasTriples()) {
            throw new NodeInUseByTriples();
        }

        $this->em->remove($node);

        $this->em->flush();
    }
}
