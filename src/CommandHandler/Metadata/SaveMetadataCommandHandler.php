<?php
declare(strict_types=1);

namespace App\CommandHandler\Metadata;

use App\Service\VersionNumberHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
abstract class SaveMetadataCommandHandler
{
    public const DEFAULT_VERSION_NUMBER = '1.0.0';

    public function __construct(protected EntityManagerInterface $em, protected Security $security, protected VersionNumberHelper $versionNumberHelper)
    {
    }

//    public function __invoke(SaveMetadataCommand $command): void
//    {
//        $catalog = $command->getCatalog();
//
//        if (! $this->security->isGranted('edit', $catalog)) {
//            throw new NoAccessPermission();
//        }
//
//        $metadata = new CatalogMetadata($catalog);
//
//        $newVersion = $this->versionNumberHelper->getNewVersion($catalog->getLatestMetadataVersion(), $command->getVersionUpdate());
//        $metadata->setVersion($newVersion);
//
//        $metadata->setTitle($this->parseLocalizedText($command->getTitle()));
//        $metadata->setDescription($this->parseLocalizedText($command->getDescription()));
//
//        if ($command->getLanguage() !== null) {
//            $metadata->setLanguage($this->getLanguage($command->getLanguage()));
//        }
//
//        if ($command->getLicense() !== null) {
//            $metadata->setLicense($this->getLicense($command->getLicense()));
//        }
//
//        if ($command->getHomepage() !== null) {
//            $metadata->setHomepage(new Iri($command->getHomepage()));
//        }
//
//        if ($command->getLogo() !== null) {
//            $metadata->setLogo(new Iri($command->getLogo()));
//        }
//
//        $metadata->setPublishers($this->parsePublishers($command->getPublishers()));
//
//        $themeTaxonomy = $this->parseOntologyConcepts($command->getThemeTaxonomy());
//
//        $metadata->setThemeTaxonomies(new ArrayCollection($themeTaxonomy));
//
//        $catalog->addMetadata($metadata);
//
//        $this->em->persist($catalog);
//        $this->em->persist($metadata);
//
//        $this->em->flush();
//    }
}
