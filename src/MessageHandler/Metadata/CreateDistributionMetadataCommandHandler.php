<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Metadata\DistributionMetadata;
use App\Message\Metadata\CreateDistributionMetadataCommand;

class CreateDistributionMetadataCommandHandler extends CreateMetadataCommandHandler
{
    public function __invoke(CreateDistributionMetadataCommand $command): void
    {
        $distribution = $command->getDistribution();
        $metadata = new DistributionMetadata($distribution);

        $newVersion = $this->updateVersionNumber($distribution->getLatestMetadataVersion(), $command->getVersionUpdate());
        $metadata->setVersion($newVersion);

        $metadata->setTitle($this->parseLocalizedText($command->getTitle()));
        $metadata->setDescription($this->parseLocalizedText($command->getDescription()));

        if ($command->getLanguage() !== null) {
            $metadata->setLanguage($this->getLanguage($command->getLanguage()));
        }

        if ($command->getLicense() !== null) {
            $metadata->setLicense($this->getLicense($command->getLicense()));
        }

        $distribution->addMetadata($metadata);

        $this->em->persist($distribution);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
