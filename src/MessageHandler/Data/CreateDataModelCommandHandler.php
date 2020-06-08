<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\DataModel;
use App\Entity\Data\DataModel\Node\RecordNode;
use App\Message\Data\CreateDataModelCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDataModelCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateDataModelCommand $command): DataModel
    {
        $dataModel = new DataModel($command->getTitle(), $command->getDescription());

        $recordNode = new RecordNode($dataModel);
        $dataModel->addNode($recordNode);

        $this->em->persist($dataModel);
        $this->em->persist($recordNode);
        $this->em->flush();

        return $dataModel;
    }
}
