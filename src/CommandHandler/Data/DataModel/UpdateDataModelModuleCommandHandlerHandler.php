<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\UpdateDataModelModuleCommand;
use App\CommandHandler\Data\DataSpecificationGroupCommandHandler;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateDataModelModuleCommandHandlerHandler extends DataSpecificationGroupCommandHandler implements MessageHandlerInterface
{
    public function __invoke(UpdateDataModelModuleCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $module = $command->getModule();

        if ($module->isDependent()) {
            $dependencies = $module->getDependencies();
            $module->setDependencies(null);
            $this->em->remove($dependencies);
        }

        $dataModelVersion = $module->getVersion();

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
