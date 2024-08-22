<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelPrefixCommand;
use App\Entity\DataSpecification\MetadataModel\NamespacePrefix;
use App\Entity\Iri;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMetadataModelPrefixCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(CreateMetadataModelPrefixCommand $command): void
    {
        $metadataModelVersion = $command->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $prefix = new NamespacePrefix($command->getPrefix(), new Iri($command->getUri()));
        $metadataModelVersion->addPrefix($prefix);

        $this->em->persist($prefix);
        $this->em->persist($metadataModelVersion);

        $this->em->flush();
    }
}
