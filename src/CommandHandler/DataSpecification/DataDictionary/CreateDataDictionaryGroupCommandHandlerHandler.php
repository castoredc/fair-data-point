<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataDictionary;

use App\Command\DataSpecification\DataDictionary\CreateDataDictionaryGroupCommand;
use App\CommandHandler\DataSpecification\Common\DataSpecificationGroupCommandHandler;
use App\Entity\DataSpecification\DataDictionary\DataDictionaryGroup;
use App\Entity\DataSpecification\DataDictionary\Variable;
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
