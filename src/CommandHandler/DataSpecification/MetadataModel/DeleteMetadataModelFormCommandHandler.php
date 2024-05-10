<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelFormCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteMetadataModelFormCommandHandler
{
    private EntityManagerInterface $em;
    private Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(DeleteMetadataModelFormCommand $command): void
    {
        $form = $command->getForm();
        $metadataModelVersion = $form->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $metadataModelVersion->removeForm($form);

        foreach ($form->getFields() as $field) {
            $this->em->remove($field);
        }

        $this->em->persist($metadataModelVersion);
        $this->em->remove($form);

        $this->em->flush();
    }
}
