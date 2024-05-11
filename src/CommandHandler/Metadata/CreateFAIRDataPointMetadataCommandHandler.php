<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateFAIRDataPointMetadataCommand;
use App\Entity\FAIRData\FAIRDataPoint;
use App\Entity\Metadata\FAIRDataPointMetadata;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateFAIRDataPointMetadataCommandHandler extends CreateMetadataCommandHandler
{
    /**
     * @throws NotFound
     * @throws NoAccessPermission
     */
    public function __invoke(CreateFAIRDataPointMetadataCommand $command): void
    {
        if (! $this->security->isGranted('ROLE_ADMIN')) {
            throw new NoAccessPermission();
        }

        /** @var FAIRDataPoint[] $fdp */
        $fdp = $this->em->getRepository(FAIRDataPoint::class)->findAll();
        $fdp = $fdp[0];

        $metadata = new FAIRDataPointMetadata($fdp);
        $metadata->setVersion(
            $this->versionNumberHelper->getNewVersion(
                $fdp->getLatestMetadataVersion(),
                $command->getVersionType()
            )
        );
        $metadata->setMetadataModelVersion(
            $this->getMetadataModelVersion(
                $command->getModelId(),
                $command->getModelVersionId()
            )
        );

        $fdp->addMetadata($metadata);

        $this->em->persist($fdp);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
