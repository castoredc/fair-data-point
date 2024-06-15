<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelModuleCommand;
use App\CommandHandler\DataSpecification\Common\DataSpecificationGroupCommandHandler;
use App\Entity\DataSpecification\MetadataModel\MetadataModelGroup;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMetadataModelModuleCommandHandler extends DataSpecificationGroupCommandHandler
{
    public function __invoke(CreateMetadataModelModuleCommand $command): void
    {
        $metadataModelVersion = $command->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $module = new MetadataModelGroup($command->getTitle(), $command->getOrder(), $command->getResourceType(), $metadataModelVersion);
        $metadataModelVersion->addGroup($module);

        $this->em->persist($module);
        $this->em->persist($metadataModelVersion);

        $this->em->flush();
    }
}
