<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\DataModelModule;
use App\Message\Data\CreateDataModelModuleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDataModelModuleCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateDataModelModuleCommand $command): void
    {
        $dataModel = $command->getDataModel();

        $module = new DataModelModule($command->getTitle(), $command->getOrder(), $dataModel);
        $dataModel->addModule($module);

        $this->em->persist($module);
        $this->em->persist($dataModel);

        $this->em->flush();
    }
}
