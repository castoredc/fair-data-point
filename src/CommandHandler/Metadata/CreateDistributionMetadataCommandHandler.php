<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateDistributionMetadataCommand;
use App\Entity\Metadata\DistributionMetadata;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateDistributionMetadataCommandHandler extends CreateMetadataCommandHandler
{
    /**
     * @throws NotFound
     * @throws NoAccessPermission
     */
    public function __invoke(CreateDistributionMetadataCommand $command): void
    {
        $distribution = $command->getDistribution();

        if (! $this->security->isGranted('edit', $distribution)) {
            throw new NoAccessPermission();
        }

        $metadata = new DistributionMetadata($distribution);
        $metadata->setVersion(
            $this->versionNumberHelper->getNewVersion(
                $distribution->getLatestMetadataVersion(),
                $command->getVersionType()
            )
        );
        $metadata->setMetadataModelVersion(
            $this->getMetadataModelVersion(
                $command->getModelId(),
                $command->getModelVersionId()
            )
        );

        $distribution->addMetadata($metadata);

        $this->em->persist($distribution);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
