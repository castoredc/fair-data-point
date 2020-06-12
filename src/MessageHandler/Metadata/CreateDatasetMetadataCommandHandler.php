<?php
declare(strict_types=1);

namespace App\MessageHandler\Metadata;

use App\Entity\Metadata\DatasetMetadata;
use App\Message\Metadata\CreateDatasetMetadataCommand;

class CreateDatasetMetadataCommandHandler extends CreateMetadataCommandHandler
{
    public function __invoke(CreateDatasetMetadataCommand $command): void
    {
        $dataset = $command->getDataset();
        $metadata = new DatasetMetadata($dataset);

        $newVersion = $this->updateVersionNumber($dataset->getLatestMetadataVersion(), $command->getVersionUpdate());
        $metadata->setVersion($newVersion);

        $metadata->setTitle($this->parseLocalizedText($command->getTitle()));
        $metadata->setDescription($this->parseLocalizedText($command->getDescription()));

        if ($command->getLanguage() !== null) {
            $metadata->setLanguage($this->getLanguage($command->getLanguage()));
        }

        if ($command->getLicense() !== null) {
            $metadata->setLicense($this->getLicense($command->getLicense()));
        }

        $dataset->addMetadata($metadata);

        $this->em->persist($dataset);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
