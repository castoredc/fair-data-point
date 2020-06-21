<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Exception\NoAccessPermission;
use App\Message\Data\UpdateDataModelModuleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class UpdateDataModelModuleCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Security */
    private $security;

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
        $dataModel = $module->getDataModel();

        $dataModel->removeModule($module);

        $module->setTitle($command->getTitle());
        $module->setOrder($command->getOrder());

        $dataModel->addModule($module);

        $this->em->persist($module);
        $this->em->persist($module->getDataModel());
        $this->em->flush();
    }
}
