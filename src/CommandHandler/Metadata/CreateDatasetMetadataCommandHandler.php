<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateDatasetMetadataCommand;
use App\Entity\Metadata\DatasetMetadata;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Security\Authorization\Voter\DatasetVoter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDatasetMetadataCommandHandler extends CreateMetadataCommandHandler
{
    /**
     * @throws NotFound
     * @throws NoAccessPermission
     */
    public function __invoke(CreateDatasetMetadataCommand $command): void
    {
        $dataset = $command->getDataset();

        if (! $this->security->isGranted(DatasetVoter::EDIT, $dataset)) {
            throw new NoAccessPermission();
        }

        $metadata = new DatasetMetadata($dataset);
        $metadata->setVersion(
            $this->versionNumberHelper->getNewVersion(
                $dataset->getLatestMetadataVersion(),
                $command->getVersionType()
            )
        );
        $metadata->setMetadataModelVersion(
            $this->getMetadataModelVersion(
                $command->getModelId(),
                $command->getModelVersionId()
            )
        );

        $dataset->addMetadata($metadata);

        $this->em->persist($dataset);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
