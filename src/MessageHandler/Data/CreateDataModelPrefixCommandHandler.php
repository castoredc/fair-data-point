<?php
declare(strict_types=1);

namespace App\MessageHandler\Data;

use App\Entity\Data\DataModel\NamespacePrefix;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use App\Message\Data\CreateDataModelPrefixCommand;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Security\Core\Security;

class CreateDataModelPrefixCommandHandler implements MessageHandlerInterface
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

    public function __invoke(CreateDataModelPrefixCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        $dataModel = $command->getDataModel();

        $prefix = new NamespacePrefix($command->getPrefix(), new Iri($command->getUri()));
        $dataModel->addPrefix($prefix);

        $this->em->persist($prefix);
        $this->em->persist($dataModel);

        $this->em->flush();
    }
}
