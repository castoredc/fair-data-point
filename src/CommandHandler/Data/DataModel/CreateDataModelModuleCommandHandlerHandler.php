<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\CreateDataModelModuleCommand;
use App\CommandHandler\Data\DataSpecificationGroupCommandHandler;
use App\Entity\Data\DataModel\DataModelGroup;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDataModelModuleCommandHandlerHandler extends DataSpecificationGroupCommandHandler implements MessageHandlerInterface
{
    public function __invoke(CreateDataModelModuleCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();

        $module = new DataModelGroup($command->getTitle(), $command->getOrder(), $command->isRepeated(), $command->isDependent(), $dataModel);
        $dataModel->addGroup($module);

        if ($command->isDependent()) {
            $dependencies = $command->getDependencies();
            $this->parseDependencies($dependencies, ValueNode::class);
            $module->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $this->em->persist($module);
        $this->em->persist($dataModel);

        $this->em->flush();
    }
}
