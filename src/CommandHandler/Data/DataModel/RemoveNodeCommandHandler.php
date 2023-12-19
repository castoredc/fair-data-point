<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\RemoveNodeCommand;
use App\Exception\NoAccessPermission;
use App\Exception\NodeInUseByTriples;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;

#[AsMessageHandler]
class RemoveNodeCommandHandler
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
        $node = $command->getNode();
        $dataModel = $node->getDataModelVersion()->getDataModel();

        if (! $this->security->isGranted('edit', $dataModel)) {
            throw new NoAccessPermission();
        }

        if ($node->hasTriples()) {
            throw new NodeInUseByTriples();
        }

        $this->em->remove($node);

        $this->em->flush();
    }
}
