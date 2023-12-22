<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataDictionary;

use App\Command\Data\DataDictionary\CreateDataDictionaryGroupCommand;
use App\CommandHandler\Data\DataSpecificationGroupCommandHandler;
use App\Entity\Data\DataDictionary\DataDictionaryGroup;
use App\Entity\Data\DataDictionary\Variable;
use App\Exception\NoAccessPermission;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDataDictionaryGroupCommandHandlerHandler extends DataSpecificationGroupCommandHandler
{
    public function __invoke(CreateDataDictionaryGroupCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataDictionaryVersion = $command->getDataDictionaryVersion();

        $group = new DataDictionaryGroup($command->getTitle(), $command->getOrder(), $command->isRepeated(), $command->isDependent(), $dataDictionaryVersion);
        $dataDictionaryVersion->addGroup($group);

        if ($command->isDependent()) {
            $dependencies = $command->getDependencies();
            $this->parseDependencies($dependencies, Variable::class);
            $group->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $this->em->persist($group);
        $this->em->persist($dataDictionaryVersion);

        $this->em->flush();
    }
}
