<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateStudyMetadataCommand;
use App\Entity\Metadata\StudyMetadata;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateStudyMetadataCommandHandler extends CreateMetadataCommandHandler
{
    /**
     * @throws NotFound
     * @throws NoAccessPermission
     */
    public function __invoke(CreateStudyMetadataCommand $command): void
    {
        $study = $command->getStudy();

        if (! $this->security->isGranted('edit', $study)) {
            throw new NoAccessPermission();
        }

        $metadata = new StudyMetadata($study);
        $metadata->setVersion(
            $this->versionNumberHelper->getNewVersion(
                $study->getLatestMetadataVersion(),
                $command->getVersionType()
            )
        );
        $metadata->setMetadataModelVersion(
            $this->getMetadataModelVersion(
                $command->getModelId(),
                $command->getModelVersionId()
            )
        );

        $study->addMetadata($metadata);

        $this->em->persist($study);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
