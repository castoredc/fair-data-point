<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\DeleteDataModelModuleCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteDataModelModuleCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
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
