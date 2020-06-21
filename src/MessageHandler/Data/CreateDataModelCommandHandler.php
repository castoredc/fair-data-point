<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Exception\NoAccessPermission;
use App\Message\Data\CreateDataModelCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDataModelCommandHandler implements MessageHandlerInterface
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

    public function __invoke(CreateDataModelCommand $command): DataModel
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = new DataModel($command->getTitle(), $command->getDescription());

        $recordNode = new RecordNode($dataModel);
        $dataModel->addNode($recordNode);

        $this->em->persist($dataModel);
        $this->em->persist($recordNode);
        $this->em->flush();

        return $dataModel;
    }
}
