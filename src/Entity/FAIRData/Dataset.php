<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Metadata\DatasetMetadata;
use App\Entity\Study;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DatasetRepository")
 * @ORM\Table(name="dataset", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Dataset implements AccessibleEntity, MetadataEnrichedEntity
{
    use CreatedAndUpdated;

    public const URL_PATH = '/fdp/dataset/';

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $slug;

    /**
     * @ORM\ManyToMany(targetEntity="Catalog", mappedBy="datasets",cascade={"persist"})
     *
     * @var Collection<string, Catalog>
     */
    private Collection $catalogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Distribution", mappedBy="dataset", cascade={"persist"})
     * @ORM\OrderBy({"slug" = "ASC"})
     *
     * @var Collection<Distribution>
     */
    private Collection $distributions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Study", inversedBy="datasets")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=TRUE)
     */
    private ?Study $study = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\DatasetMetadata", mappedBy="dataset", fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<DatasetMetadata>
     */
    private Collection $metadata;

    /** @ORM\Column(type="boolean") */
    private bool $isPublished = false;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->catalogs = new ArrayCollection();
        $this->metadata = new ArrayCollection();
        $this->distributions = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

     /**
      * @return Collection<string, Catalog>
      */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    /**
     * @param Collection<string, Catalog> $catalogs
     */
    public function setCatalogs(Collection $catalogs): void
    {
        $this->catalogs = $catalogs;
    }

    /**
     * @return Collection<string, Distribution>
     */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }

    /**
     * @param Collection<string, Distribution> $distributions
     */
    public function setDistributions(Collection $distributions): void
    {
        $this->distributions = $distributions;
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(?Study $study): void
    {
        $this->study = $study;
    }

    public function addDistribution(Distribution $distribution): void
    {
        $this->distributions[] = $distribution;
    }

    public function getRelativeUrl(): string
    {
        return self::URL_PATH . $this->slug;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    public function hasCatalog(Catalog $catalog): bool
    {
        return $this->catalogs->contains($catalog);
    }

    public function hasDistribution(Distribution $distribution): bool
    {
        return $this->distributions->contains($distribution);
    }

    public function getFirstMetadata(): ?DatasetMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->first();
    }

    public function getLatestMetadata(): ?DatasetMetadata
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

    public function addMetadata(DatasetMetadata $metadata): void
    {
        $this->metadata->add($metadata);
    }
}
