<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Metadata\FAIRDataPointMetadata;
use App\Exception\NoAccessPermission;
use App\Command\Metadata\CreateFAIRDataPointMetadataCommand;

class CreateFAIRDataPointMetadataCommandHandler extends CreateMetadataCommandHandler
{
    public function __invoke(CreateFAIRDataPointMetadataCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        /** @var FAIRDataPoint[] $fdp */
        $fdp = $this->em->getRepository(FAIRDataPoint::class)->findAll();
        $fdp = $fdp[0];

        $metadata = new FAIRDataPointMetadata($fdp);

        $newVersion = $this->versionNumberHelper->getNewVersion($fdp->getLatestMetadataVersion(), $command->getVersionUpdate());
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

        $fdp->addMetadata($metadata);

        $this->em->persist($fdp);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
