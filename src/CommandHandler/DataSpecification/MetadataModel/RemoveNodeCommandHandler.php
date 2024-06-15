<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\RemoveNodeCommand;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Exception\DataSpecification\Common\Model\NodeInUseByTriples;
use App\Exception\DataSpecification\MetadataModel\NodeHasValues;
use App\Exception\DataSpecification\MetadataModel\NodeInUseByDisplaySetting;
use App\Exception\DataSpecification\MetadataModel\NodeInUseByField;
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
     * @throws NodeInUseByField
     * @throws NodeInUseByDisplaySetting
     * @throws NodeHasValues
     */
    public function __invoke(RemoveNodeCommand $command): void
    {
        $node = $command->getNode();
        $metadataModel = $node->getMetadataModelVersion()->getMetadataModel();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        if ($node->hasTriples()) {
            throw new NodeInUseByTriples();
        }

        if ($node instanceof ValueNode && $node->hasField()) {
            throw new NodeInUseByField($node->getField()->getTitle());
        }

        if ($node instanceof ValueNode && $node->hasDisplaySetting()) {
            throw new NodeInUseByDisplaySetting($node->getDisplaySetting()->getTitle());
        }

        if ($node instanceof ValueNode && $node->hasValues()) {
            throw new NodeHasValues();
        }

        $this->em->remove($node);

        $this->em->flush();
    }
}
