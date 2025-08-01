<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataDictionary;

use App\Command\DataSpecification\DataDictionary\UpdateDataDictionaryGroupCommand;
use App\CommandHandler\DataSpecification\Common\DataSpecificationGroupCommandHandler;
use App\Entity\DataSpecification\DataDictionary\Variable;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateDataDictionaryGroupCommandHandler extends DataSpecificationGroupCommandHandler
{
    public function __invoke(UpdateDataDictionaryGroupCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $group = $command->getGroup();

        if ($group->isDependent()) {
            $dependencies = $group->getDependencies();
            $group->setDependencies(null);
            $this->em->remove($dependencies);
        }

        $dataDictionaryVersion = $group->getVersion();

        $dataDictionaryVersion->removeGroup($group);

        $group->setTitle($command->getTitle());
        $group->setOrder($command->getOrder());
        $group->setIsRepeated($command->isRepeated());
        $group->setIsDependent($command->isDependent());

        if ($command->isDependent()) {
            $dependencies = $command->getDependencies();
            $this->parseDependencies($dependencies, Variable::class);
            $group->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $dataDictionaryVersion->addGroup($group);

        $this->em->persist($group);
        $this->em->persist($dataDictionaryVersion);
        $this->em->flush();
    }
}
