<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\UpdateDataModelModuleCommand;
use App\CommandHandler\DataSpecification\Common\DataSpecificationGroupCommandHandler;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateDataModelModuleCommandHandler extends DataSpecificationGroupCommandHandler
{
    public function __invoke(UpdateDataModelModuleCommand $command): void
    {
        $module = $command->getModule();
        $dataModelVersion = $module->getVersion();
        $dataModel = $dataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $dataModel)) {
            throw new NoAccessPermission();
        }

        if ($module->isDependent()) {
            $dependencies = $module->getDependencies();
            $module->setDependencies(null);
            $this->em->remove($dependencies);
        }

        $dataModelVersion->removeGroup($module);

        $module->setTitle($command->getTitle());
        $module->setOrder($command->getOrder());
        $module->setIsRepeated($command->isRepeated());
        $module->setIsDependent($command->isDependent());

        if ($command->isDependent()) {
            $dependencies = $command->getDependencies();
            $this->parseDependencies($dependencies, ValueNode::class);
            $module->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $dataModelVersion->addGroup($module);

        $this->em->persist($module);
        $this->em->persist($dataModelVersion);
        $this->em->flush();
    }
}
