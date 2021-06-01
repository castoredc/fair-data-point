<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Connection\DistributionDatabaseInformation;
use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\Metadata\DistributionMetadata;
use App\Entity\Study;
use App\Entity\Version;
use App\Security\ApiUser;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DistributionRepository")
 * @ORM\Table(name="distribution", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Distribution implements AccessibleEntity, MetadataEnrichedEntity
{
    use CreatedAndUpdated;

    public const URL_PATH = '/fdp/distribution/';

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $slug;
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Dataset", inversedBy="distributions", cascade={"persist"})
     * @ORM\JoinColumn(name="dataset_id", referencedColumnName="id")
     */
    private ?Dataset $dataset = null;

    /** @ORM\OneToOne(targetEntity="App\Entity\Data\DistributionContents\DistributionContents", mappedBy="distribution") */
    private ?DistributionContents $contents = null;

    /** @ORM\OneToOne(targetEntity="App\Entity\Connection\DistributionDatabaseInformation", mappedBy="distribution") */
    private ?DistributionDatabaseInformation $databaseInformation = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\License",cascade={"persist"})
     * @ORM\JoinColumn(name="license", referencedColumnName="slug", nullable=true)
     */
    private ?License $license = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\DistributionMetadata", mappedBy="distribution", fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<DistributionMetadata>
     */
    private Collection $metadata;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\ApiUser")
     * @ORM\JoinColumn(name="user_api", referencedColumnName="id")
     */
    private ?ApiUser $apiUser = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="distribution_contactpoint")
     *
     * @var Collection<Agent>
     */
    private Collection $contactPoints;

    public function __construct(string $slug, Dataset $dataset)
    {
        $this->slug = $slug;
        $this->dataset = $dataset;
        $this->metadata = new ArrayCollection();
        $this->contactPoints = new ArrayCollection();
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

    public function getStudy(): ?Study
    {
        return $this->dataset->getStudy();
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

    public function getFirstMetadata(): ?DistributionMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->first();
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

    public function getApiUser(): ?ApiUser
    {
        return $this->apiUser;
    }

    public function setApiUser(?ApiUser $apiUser): void
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @return Collection<Agent>
     */
    public function getContactPoints(): Collection
    {
        return $this->contactPoints;
    }

    /**
     * @param Collection<Agent> $contactPoints
     */
    public function setContactPoints(Collection $contactPoints): void
    {
        $this->contactPoints = $contactPoints;
    }

    public function addContactPoint(Agent $contactPoint): void
    {
        $this->contactPoints->add($contactPoint);
    }

    public function removeContactPoint(Agent $contactPoint): void
    {
        $this->contactPoints->removeElement($contactPoint);
    }
}
