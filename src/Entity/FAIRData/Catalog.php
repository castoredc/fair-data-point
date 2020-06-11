<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Castor\Study;
use App\Entity\Metadata\CatalogMetadata;
use App\Entity\Version;
use App\Security\ApiUser;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Catalog
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="FAIRDataPoint", inversedBy="catalogs",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="fdp", referencedColumnName="id")
     *
     * @var FAIRDataPoint|null
     */
    private $fairDataPoint;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", inversedBy="catalogs",cascade={"persist"})
     * @ORM\JoinTable(name="catalogs_datasets")
     *
     * @var Collection<string, Dataset>
     */
    private $datasets;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $acceptSubmissions = false;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $submissionAccessesData = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\ApiUser")
     * @ORM\JoinColumn(name="user_api", referencedColumnName="id")
     *
     * @var ApiUser|null
     */
    private $apiUser;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\CatalogMetadata", mappedBy="catalog", fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<CatalogMetadata>
     */
    private $metadata;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->datasets = new ArrayCollection();
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
        if ($includeUnpublishedDatasets) {
            return $this->datasets->toArray();
        }

        $datasets = [];

        foreach ($this->datasets as $dataset) {
            /** @var Dataset $dataset */
            if (! $dataset->isPublished()) {
                continue;
            }

            $datasets[] = $dataset;
        }

        return $datasets;
    }

    /** @return Study[] */
    public function getStudies(bool $includeUnpublishedDatasets): array
    {
        $datasets = $this->getDatasets($includeUnpublishedDatasets);

        $studies = [];

        foreach ($datasets as $dataset) {
            $studies[] = $dataset->getStudy();
        }

        return $studies;
    }

    public function addDataset(Dataset $dataset): void
    {
        $this->datasets[] = $dataset;
    }

    public function getAccessUrl(): string
    {
        return $this->fairDataPoint->getAccessUrl() . '/catalog/' . $this->slug;
    }

    public function getRelativeUrl(): string
    {
        return $this->fairDataPoint->getRelativeUrl() . '/catalog/' . $this->slug;
    }

    public function getBaseUrl(): string
    {
        return $this->fairDataPoint->getIri()->getValue();
    }

    public function isAcceptingSubmissions(): bool
    {
        return $this->acceptSubmissions;
    }

    public function isSubmissionAccessingData(): bool
    {
        return $this->submissionAccessesData;
    }

    public function getApiUser(): ?ApiUser
    {
        return $this->apiUser;
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
