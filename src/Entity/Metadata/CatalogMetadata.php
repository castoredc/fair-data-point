<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Iri;
use App\Entity\Terminology\OntologyConcept;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

#[ORM\Table(name: 'metadata_catalog')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class CatalogMetadata extends Metadata
{
    #[ORM\JoinColumn(name: 'catalog', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FAIRData\Catalog::class, inversedBy: 'metadata')]
    private Catalog $catalog;

    #[ORM\Column(type: 'iri', nullable: true)]
    private ?Iri $homepage = null;

    #[ORM\Column(type: 'iri', nullable: true)]
    private ?Iri $logo = null;

    /**
     *
     * @var Collection<OntologyConcept>
     */
    #[ORM\JoinTable(name: 'metadata_catalog_themetaxonomies')]
    #[ORM\ManyToMany(targetEntity: \App\Entity\Terminology\OntologyConcept::class, cascade: ['persist'])]
    private Collection $themeTaxonomies;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;

        $this->values = new ArrayCollection();
    }

    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }

    public function setCatalog(Catalog $catalog): void
    {
        $this->catalog = $catalog;
    }

    public function getHomepage(): ?Iri
    {
        return $this->homepage;
    }

    public function setHomepage(?Iri $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function getLogo(): ?Iri
    {
        return $this->logo;
    }

    public function setLogo(?Iri $logo): void
    {
        $this->logo = $logo;
    }

    public function hasThemeTaxonomies(): bool
    {
        return count($this->themeTaxonomies) > 0;
    }

    /** @return Collection<OntologyConcept> */
    public function getThemeTaxonomies(): Collection
    {
        return $this->themeTaxonomies;
    }

    /** @param Collection<OntologyConcept> $themeTaxonomies */
    public function setThemeTaxonomies(Collection $themeTaxonomies): void
    {
        $this->themeTaxonomies = $themeTaxonomies;
    }

    public function addThemeTaxonomy(OntologyConcept $themeTaxonomy): void
    {
        $this->themeTaxonomies->add($themeTaxonomy);
    }

    public function removeThemeTaxonomy(OntologyConcept $themeTaxonomy): void
    {
        $this->themeTaxonomies->removeElement($themeTaxonomy);
    }

    public function getEntity(): ?MetadataEnrichedEntity
    {
        return $this->catalog;
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::catalog();
    }
}
