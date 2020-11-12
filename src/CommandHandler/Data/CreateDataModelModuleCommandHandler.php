<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Command\Data\CreateDataModelModuleCommand;
use App\Entity\Data\DataModel\DataModelModule;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyRule;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDataModelModuleCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateDataModelModuleCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();

        $module = new DataModelModule($command->getTitle(), $command->getOrder(), $command->isRepeated(), $command->isDependent(), $dataModel);
        $dataModel->addModule($module);

        if ($command->isDependent()) {
            $dependencies = $command->getDependencies();
            $this->parseDependencies($dependencies);
            $module->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $this->em->persist($module);
        $this->em->persist($dataModel);

        $this->em->flush();
    }

    private function parseDependencies(DataModelDependencyGroup $group): void
    {
        foreach ($group->getRules() as $rule) {
            if ($rule instanceof DataModelDependencyGroup) {
                $this->parseDependencies($rule);
            } elseif ($rule instanceof DataModelDependencyRule) {
                $node = $this->em->getRepository(ValueNode::class)->find($rule->getNodeId());

                if ($node === null) {
                    throw new NotFound();
                }

                $rule->setNode($node);
            }
        }
    }
}
