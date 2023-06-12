<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use App\Entity\Metadata\FAIRDataPointMetadata;
use App\Entity\Version;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity
 * @ORM\Table(name="fdp", indexes={@ORM\Index(name="iri", columns={"iri"})})
 */
class FAIRDataPoint implements AccessibleEntity, MetadataEnrichedEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="iri") */
    private Iri $iri;

    /** @ORM\Column(type="iri", nullable=true) */
    private ?Iri $purl;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\FAIRDataPointMetadata", mappedBy="fdp", fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<FAIRDataPointMetadata>
     */
    private Collection $metadata;

    /**
     * @ORM\OneToMany(targetEntity="Catalog", mappedBy="fairDataPoint",cascade={"persist"}, fetch = "EAGER")
     *
     * @var Collection<string, Catalog>
     */
    private Collection $catalogs;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isArchived = false;

    public function __construct()
    {
        $this->metadata = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getIri(): Iri
    {
        return $this->iri;
    }

    public function setIri(Iri $iri): void
    {
        $this->iri = $iri;
    }

    public function getPurl(): ?Iri
    {
        return $this->purl;
    }

    public function setPurl(?Iri $purl): void
    {
        $this->purl = $purl;
    }

    public function getRelativeUrl(): string
    {
        return '/fdp';
    }

    /** @return Collection<string, Catalog> */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    /** @param Collection<string, Catalog> $catalogs */
    public function setCatalogs(Collection $catalogs): void
    {
        $this->catalogs = $catalogs;
    }

    public function addCatalog(Catalog $catalog): void
    {
        $this->catalogs->add($catalog);
    }

    public function getFirstMetadata(): ?FAIRDataPointMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->first();
    }

    public function getLatestMetadata(): ?FAIRDataPointMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->last();
    }

    public function getLatestMetadataVersion(): ?Version
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->last()->getVersion();
    }

    public function hasMetadata(): bool
    {
        return count($this->metadata) > 0;
    }

    public function addMetadata(FAIRDataPointMetadata $metadata): void
    {
        $this->metadata->add($metadata);
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }
}
