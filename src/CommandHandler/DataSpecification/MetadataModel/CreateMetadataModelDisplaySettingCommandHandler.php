<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelDisplaySettingCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelDisplaySetting;
use App\Entity\DataSpecification\MetadataModel\Node\Node;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Exception\DataSpecification\MetadataModel\NodeAlreadyUsed;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use function assert;

#[AsMessageHandler]
class CreateMetadataModelDisplaySettingCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    public function __invoke(CreateMetadataModelDisplaySettingCommand $command): void
    {
        $metadataModelVersion = $command->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $nodeRepository = $this->em->getRepository(Node::class);
        $node = $nodeRepository->find($command->getNode());

        if ($node === null) {
            throw new NotFound();
        }

        assert($node instanceof ValueNode);

        if ($node->hasDisplaySetting()) {
            throw new NodeAlreadyUsed();
        }

        $displaySetting = new MetadataModelDisplaySetting(
            $command->getTitle(),
            $command->getOrder(),
            $node,
            $command->getDisplayType(),
            $command->getDisplayPosition(),
            $command->getResourceType(),
            $command->getMetadataModelVersion(),
        );

        $metadataModelVersion->addDisplaySetting($displaySetting);

        $this->em->persist($displaySetting);

        $this->em->flush();
    }
}
