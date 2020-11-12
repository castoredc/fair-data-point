<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Command\Metadata\CreateCatalogMetadataCommand;
use App\Entity\Iri;
use App\Entity\Metadata\CatalogMetadata;
use App\Exception\NoAccessPermission;
use Doctrine\Common\Collections\ArrayCollection;

class CreateCatalogMetadataCommandHandler extends CreateMetadataCommandHandler
{
    public function __invoke(CreateCatalogMetadataCommand $command): void
    {
        $catalog = $command->getCatalog();

        if (! $this->security->isGranted('edit', $catalog)) {
            throw new NoAccessPermission();
        }

        $metadata = new CatalogMetadata($catalog);

        $newVersion = $this->versionNumberHelper->getNewVersion($catalog->getLatestMetadataVersion(), $command->getVersionUpdate());
        $metadata->setVersion($newVersion);

        $metadata->setTitle($this->parseLocalizedText($command->getTitle()));
        $metadata->setDescription($this->parseLocalizedText($command->getDescription()));

        if ($command->getLanguage() !== null) {
            $metadata->setLanguage($this->getLanguage($command->getLanguage()));
        }

        if ($command->getLicense() !== null) {
            $metadata->setLicense($this->getLicense($command->getLicense()));
        }

        if ($command->getHomepage() !== null) {
            $metadata->setHomepage(new Iri($command->getHomepage()));
        }

        if ($command->getLogo() !== null) {
            $metadata->setLogo(new Iri($command->getLogo()));
        }

        $metadata->setPublishers($this->parsePublishers($command->getPublishers()));

        $themeTaxonomy = $this->parseOntologyConcepts($command->getThemeTaxonomy());

        $metadata->setThemeTaxonomies(new ArrayCollection($themeTaxonomy));

        $catalog->addMetadata($metadata);

        $this->em->persist($catalog);
        $this->em->persist($metadata);

        $this->em->flush();
    }
}
