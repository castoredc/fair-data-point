<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Message\Data\DeleteDataModelModuleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class DeleteDataModelModuleCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(DeleteDataModelModuleCommand $command): void
    {
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
