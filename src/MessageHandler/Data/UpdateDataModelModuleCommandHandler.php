<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Message\Data\UpdateDataModelModuleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateDataModelModuleCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(UpdateDataModelModuleCommand $command): void
    {
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
