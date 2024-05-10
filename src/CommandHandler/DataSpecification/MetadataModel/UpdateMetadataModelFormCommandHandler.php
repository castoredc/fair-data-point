<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\UpdateMetadataModelFormCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateMetadataModelFormCommandHandler
{
    protected EntityManagerInterface $em;

    protected Security $security;

    public function __construct(EntityManagerInterface $em, Security $security)
    {
        $this->em = $em;
        $this->security = $security;
    }

    public function __invoke(UpdateMetadataModelFormCommand $command): void
    {
        $form = $command->getForm();
        $metadataModelVersion = $form->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $metadataModelVersion->removeForm($form);

        $form->setTitle($command->getTitle());
        $form->setOrder($command->getOrder());

        $metadataModelVersion->addForm($form);

        $this->em->persist($form);
        $this->em->persist($metadataModelVersion);
        $this->em->flush();
    }
}
