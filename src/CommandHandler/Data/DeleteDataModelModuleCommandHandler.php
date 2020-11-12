<?php
declare(strict_types=1);

namespace App\CommandHandler\Data;

use App\Command\Data\DataModel\DeleteDataModelModuleCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class DeleteDataModelModuleCommandHandler implements MessageHandlerInterface
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
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $module = $command->getModule();
        $dataModel = $module->getDataModel();
        $dataModel->removeModule($module);

        foreach ($module->getTriples() as $triple) {
            $this->em->remove($triple);
        }

        $this->em->persist($module->getDataModel());
        $this->em->remove($module);

        $this->em->flush();
    }
}
