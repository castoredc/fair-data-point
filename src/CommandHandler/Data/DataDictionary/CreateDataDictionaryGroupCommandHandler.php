<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataDictionary;

use App\Command\Data\DataDictionary\CreateDataDictionaryGroupCommand;
use App\Entity\Data\DataDictionary\DataDictionaryGroup;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyGroup;
use App\Entity\Data\DataDictionary\Dependency\DataDictionaryDependencyRule;
use App\Entity\Data\DataDictionary\Variable;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDataDictionaryGroupCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

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
            $this->parseDependencies($dependencies);
            $group->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $this->em->persist($group);
        $this->em->persist($dataDictionaryVersion);

        $this->em->flush();
    }

    private function parseDependencies(DataDictionaryDependencyGroup $group): void
    {
        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DataDictionaryDependencyGroup) {
                $this->parseDependencies($rule);
            } elseif ($rule instanceof DataDictionaryDependencyRule) {
                $variable = $this->em->getRepository(Variable::class)->find($rule->getVariableId());

                if ($variable === null) {
                    throw new NotFound();
                }

                $rule->setVariable($variable);
            }
        }
    }
}
