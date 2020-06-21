<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\DataModelModule;
use App\Exception\NoAccessPermission;
use App\Message\Data\CreateDataModelModuleCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDataModelModuleCommandHandler implements MessageHandlerInterface
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

    public function __invoke(CreateDataModelModuleCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();

        $module = new DataModelModule($command->getTitle(), $command->getOrder(), $dataModel);
        $dataModel->addModule($module);

        $this->em->persist($module);
        $this->em->persist($dataModel);

        $this->em->flush();
    }
}
