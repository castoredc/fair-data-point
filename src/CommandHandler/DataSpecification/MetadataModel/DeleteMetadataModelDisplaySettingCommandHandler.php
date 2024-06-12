<?php
declare(strict_types=1);

namespace App\CommandHandler\DataSpecification\MetadataModel;

use App\Command\DataSpecification\MetadataModel\DeleteMetadataModelDisplaySettingCommand;
use App\Exception\NoAccessPermission;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class DeleteMetadataModelDisplaySettingCommandHandler
{
    public function __construct(private EntityManagerInterface $em, private Security $security)
    {
    }

    public function __invoke(DeleteMetadataModelDisplaySettingCommand $command): void
    {
        $displaySetting = $command->getDisplaySetting();
        $metadataModelVersion = $displaySetting->getMetadataModelVersion();
        $metadataModel = $metadataModelVersion->getDataSpecification();

        if (! $this->security->isGranted('edit', $metadataModel)) {
            throw new NoAccessPermission();
        }

        $metadataModelVersion->removeDisplaySetting($displaySetting);

        $this->em->remove($displaySetting);

        $this->em->flush();
    }
}
