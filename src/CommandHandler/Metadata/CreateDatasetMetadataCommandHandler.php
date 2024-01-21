<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateDatasetMetadataCommand;
use App\Entity\Metadata\DatasetMetadata;
use App\Exception\NoAccessPermission;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDatasetMetadataCommandHandler extends CreateMetadataCommandHandler
{
    public function __invoke(CreateDatasetMetadataCommand $command): void
    {
        $dataset = $command->getDataset();

        if (! $this->security->isGranted('edit', $dataset)) {
            throw new NoAccessPermission();
        }

        $metadata = new DatasetMetadata($dataset);

        $newVersion = $this->versionNumberHelper->getNewVersion($dataset->getLatestMetadataVersion(), $command->getVersionUpdate());
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

        $theme = $this->parseOntologyConcepts($command->getTheme());

        $metadata->setThemes(new ArrayCollection($theme));
        $metadata->setKeyword($this->parseLocalizedText($command->getKeyword()));

        $dataset->addMetadata($metadata);

        $this->em->persist($dataset);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
