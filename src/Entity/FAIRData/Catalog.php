<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Enum\PermissionType;
use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Permission\CatalogPermission;
use App\Entity\Metadata\CatalogMetadata;
use App\Entity\Study;
use App\Entity\Version;
use App\Security\PermissionsEnabledEntity;
use App\Security\User;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use function array_merge;
use function array_unique;
use function count;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CatalogRepository")
 * @ORM\Table(name="catalog", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Catalog implements AccessibleEntity, MetadataEnrichedEntity, PermissionsEnabledEntity
{
    use CreatedAndUpdated;

    public const URL_PATH = '/fdp/catalog/';

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string $id;

    /** @ORM\Column(type="string", unique=true) */
    private string $slug;

    /**
     * @ORM\ManyToOne(targetEntity="FAIRDataPoint", inversedBy="catalogs",cascade={"persist"})
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

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isArchived = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\CatalogMetadata", mappedBy="catalog")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<CatalogMetadata>
     */
    private Collection $metadata;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Permission\CatalogPermission", cascade={"persist", "remove"}, orphanRemoval=true, mappedBy="catalog")
     *
     * @var Collection<CatalogPermission>
     */
    private Collection $permissions;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DataSpecification\MetadataModel\MetadataModel", inversedBy="catalogs")
     * @ORM\JoinColumn(name="default_metadata_model_id", referencedColumnName="id")
     */
    private ?MetadataModel $defaultMetadataModel = null;

    public function __construct(string $slug)
    {
        $this->slug = $slug;

        $this->datasets = new ArrayCollection();
        $this->studies = new ArrayCollection();
        $this->metadata = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
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

    /** @return Dataset[] */
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
        return self::URL_PATH . $this->slug;
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

    public function getFirstMetadata(): ?CatalogMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->first();
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

    public function isPublic(): bool
    {
        return true;
    }

    /** @return Collection<CatalogPermission> */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermissionForUser(User $user, PermissionType $type): CatalogPermission
    {
        $permission = new CatalogPermission($user, $type, $this);
        $this->permissions->add($permission);

        return $permission;
    }

    public function removePermissionForUser(User $user): void
    {
        $permission = $this->getPermissionsForUser($user);
        $this->permissions->removeElement($permission);
    }

    public function getPermissionsForUser(User $user): ?CatalogPermission
    {
        foreach ($this->permissions->toArray() as $permission) {
            if ($permission->getUser() === $user) {
                return $permission;
            }
        }

        return null;
    }

    /** @return PermissionType[] */
    public function supportsPermissions(): array
    {
        return [
            PermissionType::view(),
            PermissionType::edit(),
            PermissionType::manage(),
        ];
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    public function getDefaultMetadataModel(): ?MetadataModel
    {
        return $this->defaultMetadataModel;
    }

    public function setDefaultMetadataModel(?MetadataModel $defaultMetadataModel): void
    {
        $this->defaultMetadataModel = $defaultMetadataModel;
    }

    /** @return Distribution[] */
    public function getDistributions(): array
    {
        return array_unique(array_merge(
            $this->datasets->map(static function (Dataset $dataset) {
                return $dataset->getDistributions()->toArray();
            })->toArray(),
            $this->studies->map(static function (Study $study) {
                return $study->getDistributions();
            })->toArray()
        ));
    }

    /** @return MetadataEnrichedEntity[] */
    public function getChildren(ResourceType $resourceType): array
    {
        if ($resourceType->isDataset()) {
            return $this->getDatasets(true);
        }

        if ($resourceType->isStudy()) {
            return $this->studies->toArray();
        }

        if ($resourceType->isDistribution()) {
            return $this->getDistributions();
        }

        return [];
    }

    /** @return MetadataEnrichedEntity[] */
    public function getParents(ResourceType $resourceType): array
    {
        if ($resourceType->isFdp()) {
            return [$this->fairDataPoint];
        }

        return [];
    }
}
