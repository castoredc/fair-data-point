<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\VersionType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\Terminology\OntologyConcept;

class CreateCatalogMetadataCommand extends CreateMetadataCommand
{
    private Catalog $catalog;

    private ?string $homepage = null;

    private ?string $logo = null;

    /** @var OntologyConcept[] */
    private array $themeTaxonomy;

    /**
     * @param Agent[]           $publishers
     * @param OntologyConcept[] $themeTaxonomy
     */
    public function __construct(
        Catalog $catalog,
        ?LocalizedText $title,
        ?LocalizedText $description,
        ?string $language,
        ?string $license,
        VersionType $versionUpdate,
        array $publishers,
        ?string $homepage,
        ?string $logo,
        array $themeTaxonomy
    ) {
        parent::__construct($title, $description, $language, $license, $versionUpdate, $publishers);

        $this->catalog = $catalog;
        $this->homepage = $homepage;
        $this->logo = $logo;
        $this->themeTaxonomy = $themeTaxonomy;
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    /**
     * @return OntologyConcept[]
     */
    public function getThemeTaxonomy(): array
    {
        return $this->themeTaxonomy;
    }
}
