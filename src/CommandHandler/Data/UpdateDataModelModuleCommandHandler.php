<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Command\Data\UpdateDataModelModuleCommand;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyGroup;
use App\Entity\Data\DataModel\Dependency\DataModelDependencyRule;
use App\Entity\Data\DataModel\Node\ValueNode;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateDataModelModuleCommandHandler implements MessageHandlerInterface
{
    private EntityManagerInterface $em;

    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

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

        $dataModel = $module->getDataModel();

        $dataModel->removeModule($module);

        $module->setTitle($command->getTitle());
        $module->setOrder($command->getOrder());
        $module->setIsRepeated($command->isRepeated());
        $module->setIsDependent($command->isDependent());

        if ($command->isDependent()) {
            $dependencies = $command->getDependencies();
            $this->parseDependencies($dependencies);
            $module->setDependencies($dependencies);

            $this->em->persist($dependencies);
        }

        $dataModel->addModule($module);

        $this->em->persist($module);
        $this->em->persist($module->getDataModel());
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
