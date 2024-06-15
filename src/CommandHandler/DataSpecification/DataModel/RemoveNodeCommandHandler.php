<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\RemoveNodeCommand;
use App\Exception\DataSpecification\Common\Model\NodeInUseByTriples;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RemoveNodeCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
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
