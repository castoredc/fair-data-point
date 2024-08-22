<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\CreateMetadataModelFormCommand;
use App\Entity\DataSpecification\MetadataModel\MetadataModelForm;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateMetadataModelFormCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    public function __invoke(CreateMetadataModelFormCommand $command): void
    {
        $metadataModelVersion = $command->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getMetadataModel();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $form = new MetadataModelForm($command->getTitle(), $command->getOrder(), $command->getResourceType(), $metadataModelVersion);
        $metadataModelVersion->addForm($form);

        $this->em->persist($form);
        $this->em->persist($metadataModelVersion);

        $this->em->flush();
    }
}
