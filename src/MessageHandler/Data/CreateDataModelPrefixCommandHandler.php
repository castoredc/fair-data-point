<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Iri;
use App\Message\Data\CreateDataModelPrefixCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateDataModelPrefixCommandHandler implements MessageHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(CreateDataModelPrefixCommand $command): void
    {
        $dataModel = $command->getDataModel();

        $prefix = new NamespacePrefix($command->getPrefix(), new Iri($command->getUri()));
        $dataModel->addPrefix($prefix);

        $this->em->persist($prefix);
        $this->em->persist($dataModel);

        $this->em->flush();
    }
}
