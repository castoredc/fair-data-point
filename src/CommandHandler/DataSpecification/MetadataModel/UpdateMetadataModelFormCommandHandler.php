<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelFormCommand;
use App\Exception\NoAccessPermission;
use App\Security\Authorization\Voter\DataSpecificationVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMetadataModelFormCommandHandler
{
    public function __construct(protected EntityManagerInterface $em, protected Security $security)
    {
    }

    public function __invoke(UpdateMetadataModelFormCommand $command): void
    {
        $form = $command->getForm();
        $metadataModelVersion = $form->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted(DataSpecificationVoter::EDIT, $metadataModel)) {
            throw new NoAccessPermission();
        }

        $metadataModelVersion->removeForm($form);

        $form->setTitle($command->getTitle());
        $form->setOrder($command->getOrder());
        $form->setResourceType($command->getResourceType());

        $metadataModelVersion->addForm($form);

        $this->em->persist($form);
        $this->em->persist($metadataModelVersion);
        $this->em->flush();
    }
}
