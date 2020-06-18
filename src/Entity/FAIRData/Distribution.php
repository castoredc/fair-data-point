<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Connection\DistributionDatabaseInformation;
use App\Entity\Data\DistributionContents;
use App\Entity\Metadata\DistributionMetadata;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Distribution implements AccessibleEntity
{
    use CreatedAndUpdated;

    public const TYPE_RDF = 'rdf';
    public const TYPE_CSV = 'csv';

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
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Dataset", inversedBy="distributions", cascade={"persist"})
     * @ORM\JoinColumn(name="dataset_id", referencedColumnName="id")
     *
     * @var Dataset|null
     */
    private $dataset;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Data\DistributionContents", mappedBy="distribution")
     *
     * @var DistributionContents|null
     */
    private $contents;

    /**
     * @ORM\OneToOne(targetEntity="App\Connection\DistributionDatabaseInformation", mappedBy="distribution")
     *
     * @var DistributionDatabaseInformation|null
     */
    private $databaseInformation;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\License",cascade={"persist"})
     * @ORM\JoinColumn(name="license", referencedColumnName="slug", nullable=true)
     *
     * @var License|null
     */
    private $license;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\DistributionMetadata", mappedBy="distribution", fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<DistributionMetadata>
     */
    private $metadata;

    public function __construct(string $slug, Dataset $dataset)
    {
        $this->slug = $slug;
        $this->dataset = $dataset;
        $this->metadata = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    public function setDataset(Dataset $dataset): void
    {
        $this->dataset = $dataset;
    }

    public function getRelativeUrl(): string
    {
        return $this->dataset->getRelativeUrl() . '/distribution/' . $this->slug;
    }

    public function hasContents(): bool
    {
        return $this->contents !== null;
    }

    public function getContents(): ?DistributionContents
    {
        return $this->contents;
    }

    public function setContents(?DistributionContents $contents): void
    {
        $this->contents = $contents;
    }

    public function setDatabaseInformation(DistributionDatabaseInformation $databaseInformation): void
    {
        $this->databaseInformation = $databaseInformation;
    }

    public function getDatabaseInformation(): ?DistributionDatabaseInformation
    {
        return $this->databaseInformation;
    }

    public function getLatestMetadata(): ?DistributionMetadata
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

    public function addMetadata(DistributionMetadata $metadata): void
    {
        $this->metadata->add($metadata);
    }

    public function getLicense(): ?License
    {
        return $this->license;
    }

    public function setLicense(?License $license): void
    {
        $this->license = $license;
    }
}
