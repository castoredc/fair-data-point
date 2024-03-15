<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\DataModel;

use App\Command\DataSpecification\DataModel\CreateDataModelPrefixCommand;
use App\Entity\DataSpecification\DataModel\NamespacePrefix;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDataModelPrefixCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(CreateDataModelPrefixCommand $command): void
    {
        $dataModelVersion = $command->getDataModelVersion();
        $dataModel = $dataModelVersion->getDataModel();

        if (! $this->security->isGranted('edit', $dataModel)) {
            throw new NoAccessPermission();
        }

        $prefix = new NamespacePrefix($command->getPrefix(), new Iri($command->getUri()));
        $dataModelVersion->addPrefix($prefix);

        $this->em->persist($prefix);
        $this->em->persist($dataModelVersion);

        $this->em->flush();
    }
}
