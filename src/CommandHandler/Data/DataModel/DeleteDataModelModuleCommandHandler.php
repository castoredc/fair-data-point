<?php
declare(strict_types=1);

namespace App\CommandHandler\Data\DataModel;

use App\Command\Data\DataModel\DeleteDataModelModuleCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Security\Core\Security;

#[AsMessageHandler]
class DeleteDataModelModuleCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(DeleteDataModelModuleCommand $command): void
    {
        $module = $command->getModule();
        $dataModelVersion = $module->getVersion();
        $dataModel = $dataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $dataModel)) {
            throw new NoAccessPermission();
        }

        $dataModelVersion->removeGroup($module);

        foreach ($module->getElementGroups() as $triple) {
            $this->em->remove($triple);
        }

        $this->em->persist($module->getVersion());
        $this->em->remove($module);

        $this->em->flush();
    }
}
