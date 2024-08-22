<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateCatalogMetadataCommand;
use App\Entity\Metadata\CatalogMetadata;
use App\Exception\NoAccessPermission;
use App\Exception\NotFound;
use App\Security\Authorization\Voter\CatalogVoter;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateCatalogMetadataCommandHandler extends CreateMetadataCommandHandler
{
    /**
     * @throws NotFound
     * @throws NoAccessPermission
     */
    public function __invoke(CreateCatalogMetadataCommand $command): void
    {
        $catalog = $command->getCatalog();

        if (! $this->security->isGranted(CatalogVoter::EDIT, $catalog)) {
            throw new NoAccessPermission();
        }

        $metadata = new CatalogMetadata($catalog);
        $metadata->setVersion(
            $this->versionNumberHelper->getNewVersion(
                $catalog->getLatestMetadataVersion(),
                $command->getVersionType()
            )
        );
        $metadata->setMetadataModelVersion(
            $this->getMetadataModelVersion(
                $command->getModelId(),
                $command->getModelVersionId()
            )
        );

        $catalog->addMetadata($metadata);

        $this->em->persist($catalog);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
