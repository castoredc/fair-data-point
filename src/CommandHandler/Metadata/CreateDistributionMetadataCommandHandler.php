<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Entity\Metadata\DistributionMetadata;
use App\Exception\NoAccessPermission;
use App\Command\Metadata\CreateDistributionMetadataCommand;

class CreateDistributionMetadataCommandHandler extends CreateMetadataCommandHandler
{
    public function __invoke(CreateDistributionMetadataCommand $command): void
    {
        $distribution = $command->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $metadata = new DistributionMetadata($distribution);

        $newVersion = $this->versionNumberHelper->getNewVersion($distribution->getLatestMetadataVersion(), $command->getVersionUpdate());
        $metadata->setVersion($newVersion);

        $metadata->setTitle($this->parseLocalizedText($command->getTitle()));
        $metadata->setDescription($this->parseLocalizedText($command->getDescription()));

        if ($command->getLanguage() !== null) {
            $metadata->setLanguage($this->getLanguage($command->getLanguage()));
        }

        if ($command->getLicense() !== null) {
            $metadata->setLicense($this->getLicense($command->getLicense()));
        }

        $metadata->setPublishers($this->parsePublishers($command->getPublishers()));

        $distribution->addMetadata($metadata);

        $this->em->persist($distribution);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
