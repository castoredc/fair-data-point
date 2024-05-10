<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelModuleCommand;
use App\CommandHandler\DataSpecification\Common\DataSpecificationGroupCommandHandler;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMetadataModelModuleCommandHandler extends DataSpecificationGroupCommandHandler
{
    public function __invoke(UpdateMetadataModelModuleCommand $command): void
    {
        $module = $command->getModule();
        $metadataModelVersion = $module->getVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        if ($module->isDependent()) {
            $dependencies = $module->getDependencies();
            $module->setDependencies(null);
            $this->em->remove($dependencies);
        }

        $metadataModelVersion->removeGroup($module);

        $module->setTitle($command->getTitle());
        $module->setOrder($command->getOrder());

        $metadataModelVersion->addGroup($module);

        $this->em->persist($module);
        $this->em->persist($metadataModelVersion);
        $this->em->flush();
    }
}
