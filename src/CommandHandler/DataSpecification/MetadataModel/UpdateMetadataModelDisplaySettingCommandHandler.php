<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelDisplaySettingCommand;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Exception\DataSpecification\MetadataModel\NodeAlreadyUsed;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMetadataModelDisplaySettingCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    public function __invoke(UpdateMetadataModelDisplaySettingCommand $command): void
    {
        $displaySetting = $command->getDisplaySetting();
        $metadataModelVersion = $displaySetting->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $nodeRepository = $this->em->getRepository(Node::class);

        $node = $nodeRepository->find($command->getNode());

        if (! ($node instanceof ValueNode)) {
            throw new NotFound();
        }

        if ($node->hasDisplaySetting() && $node->getDisplaySetting() !== $displaySetting) {
            throw new NodeAlreadyUsed();
        }

        $metadataModelVersion->removeDisplaySetting($displaySetting);

        $displaySetting->setTitle($command->getTitle());
        $displaySetting->setOrder($command->getOrder());
        $displaySetting->setNode($node);
        $displaySetting->setDisplayType($command->getDisplayType());
        $displaySetting->setDisplayPosition($command->getDisplayPosition());

        $metadataModelVersion->addDisplaySetting($displaySetting);

        $this->em->persist($displaySetting);
        $this->em->flush();
    }
}
