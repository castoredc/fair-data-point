<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\CreateDataModelModuleCommand;
use App\CommandHandler\DataSpecification\DataSpecificationGroupCommandHandler;
use App\Entity\DataSpecification\DataModel\DataModelGroup;
use App\Entity\DataSpecification\DataModel\Node\ValueNode;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDataModelModuleCommandHandlerHandler extends DataSpecificationGroupCommandHandler
{
    public function __invoke(CreateDataModelModuleCommand $command): void
    {
        $dataModelVersion = $command->getDataModelVersion();
        $dataModel = $dataModelVersion->getDataModel();

        if (! $this->security->isGranted('edit', $dataModel)) {
            throw new NoAccessPermission();
        }

        $module = new DataModelGroup($command->getTitle(), $command->getOrder(), $command->isRepeated(), $command->isDependent(), $dataModelVersion);
        $dataModelVersion->addGroup($module);

        if ($command->isDependent()) {
            $dependencies = $command->getDependencies();
            $this->parseDependencies($dependencies, ValueNode::class);
            $module->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $this->em->persist($module);
        $this->em->persist($dataModelVersion);

        $this->em->flush();
    }
}
