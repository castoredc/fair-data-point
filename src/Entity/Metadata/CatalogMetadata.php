<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\FAIRData\Catalog;
use App\Entity\Iri;
use App\Entity\Terminology\OntologyConcept;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_catalog")
 * @ORM\HasLifecycleCallbacks
 */
class CatalogMetadata extends Metadata
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Catalog", inversedBy="metadata", fetch="EAGER")
     * @ORM\JoinColumn(name="catalog", referencedColumnName="id", nullable=FALSE)
     */
    private Catalog $catalog;

    /** @ORM\Column(type="iri", nullable=true) */
    private ?Iri $homepage = null;

    /** @ORM\Column(type="iri", nullable=true) */
    private ?Iri $logo = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Terminology\OntologyConcept",cascade={"persist"})
     * @ORM\JoinTable(name="metadata_catalog_themetaxonomies")
     *
     * @var Collection<OntologyConcept>
     */
    private Collection $themeTaxonomies;

    public function __construct(Catalog $catalog)
    {
        $this->catalog = $catalog;
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
}
