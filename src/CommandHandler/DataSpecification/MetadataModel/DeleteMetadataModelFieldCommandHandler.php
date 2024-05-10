<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelFieldCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteMetadataModelFieldCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(DeleteMetadataModelFieldCommand $command): void
    {
        $field = $command->getField();
        $form = $field->getForm();
        $metadataModel = $form->getMetadataModelVersion()->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $form->removeField($field);
        $form->reorderFields();

        $this->em->persist($form);
        $this->em->remove($field);

        $this->em->flush();
    }
}
