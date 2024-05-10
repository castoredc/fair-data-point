<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelModuleCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteMetadataModelModuleCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(DeleteMetadataModelModuleCommand $command): void
    {
        $module = $command->getModule();
        $metadataModelVersion = $module->getVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $metadataModelVersion->removeGroup($module);

        foreach ($module->getElementGroups() as $triple) {
            $this->em->remove($triple);
        }

        $this->em->persist($module->getVersion());
        $this->em->remove($module);

        $this->em->flush();
    }
}
