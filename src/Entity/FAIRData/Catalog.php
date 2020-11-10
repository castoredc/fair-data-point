<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Metadata\CatalogMetadata;
use App\Entity\Study;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_merge;
use function count;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Catalog implements AccessibleEntity, MetadataEnrichedEntity
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $slug;

    /**
     * @ORM\ManyToOne(targetEntity="FAIRDataPoint", inversedBy="catalogs",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="fdp", referencedColumnName="id")
     */
    private ?FAIRDataPoint $fairDataPoint = null;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", inversedBy="catalogs",cascade={"persist"})
     * @ORM\JoinTable(name="catalogs_datasets")
     *
     * @var Collection<Dataset>
     */
    private Collection $datasets;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Study", inversedBy="catalogs", cascade={"persist"})
     * @ORM\JoinTable(name="catalogs_studies")
     *
     * @var Collection<Study>
     */
    private Collection $studies;

    /** @ORM\Column(type="boolean") */
    private bool $acceptSubmissions = false;

    /** @ORM\Column(type="boolean") */
    private bool $submissionAccessesData = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\CatalogMetadata", mappedBy="catalog", fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<CatalogMetadata>
     */
    private Collection $metadata;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->datasets = new ArrayCollection();
        $this->studies = new ArrayCollection();
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

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getFairDataPoint(): FAIRDataPoint
    {
        return $this->fairDataPoint;
    }

    public function setFairDataPoint(FAIRDataPoint $fairDataPoint): void
    {
        $this->fairDataPoint = $fairDataPoint;
    }

    /**
     * @return Dataset[]
     */
    public function getDatasets(bool $includeUnpublishedDatasets): array
    {
        $datasets = $this->datasets->toArray();
        $return = [];

        foreach ($this->getStudies($includeUnpublishedDatasets) as $study) {
            $datasets = array_merge($datasets, $study->getDatasets()->toArray());
        }

        if ($includeUnpublishedDatasets) {
            return $datasets;
        }

        foreach ($datasets as $dataset) {
            if (! $dataset->isPublished()) {
                continue;
            }

            $return[] = $dataset;
        }

        return $return;
    }

    /** @return Study[] */
    public function getStudies(bool $includeUnpublishedStudies): array
    {
        if ($includeUnpublishedStudies) {
            return $this->studies->toArray();
        }

        $studies = [];

        foreach ($this->studies as $study) {
            if (! $study->isPublished()) {
                continue;
            }

            $studies[] = $study;
        }

        return $studies;
    }

    public function addDataset(Dataset $dataset): void
    {
        if ($this->datasets->contains($dataset)) {
            return;
        }

        $this->datasets->add($dataset);
    }

    public function removeDataset(Dataset $dataset): void
    {
        $this->datasets->removeElement($dataset);
    }

    public function addStudy(Study $study): void
    {
        if ($this->studies->contains($study)) {
            return;
        }

        $this->studies->add($study);
    }

    public function removeStudy(Study $study): void
    {
        $this->studies->removeElement($study);
    }

    public function getRelativeUrl(): string
    {
        return '/fdp/catalog/' . $this->slug;
    }

    public function getBaseUrl(): string
    {
        return $this->fairDataPoint->getIri()->getValue();
    }

    public function isAcceptingSubmissions(): bool
    {
        return $this->acceptSubmissions;
    }

    public function setAcceptSubmissions(bool $acceptSubmissions): void
    {
        $this->acceptSubmissions = $acceptSubmissions;
    }

    public function isSubmissionAccessingData(): bool
    {
        return $this->submissionAccessesData;
    }

    public function setSubmissionAccessesData(bool $submissionAccessesData): void
    {
        $this->submissionAccessesData = $submissionAccessesData;
    }

    public function getLatestMetadata(): ?CatalogMetadata
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

    public function addMetadata(CatalogMetadata $metadata): void
    {
        $this->metadata->add($metadata);
    }
}
