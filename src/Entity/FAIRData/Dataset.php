<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Enum\PermissionType;
use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Permission\DatasetPermission;
use App\Entity\Metadata\DatasetMetadata;
use App\Entity\Study;
use App\Entity\Version;
use App\Repository\DatasetRepository;
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

#[ORM\Table(name: 'dataset')]
#[ORM\Index(name: 'slug', columns: ['slug'])]
#[ORM\Entity(repositoryClass: DatasetRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Dataset implements AccessibleEntity, MetadataEnrichedEntity, PermissionsEnabledEntity
{
    use CreatedAndUpdated;

    public const URL_PATH = '/fdp/dataset/';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $slug;

    /** @var Collection<string, Catalog> */
    #[ORM\ManyToMany(targetEntity: Catalog::class, mappedBy: 'datasets', cascade: ['persist'])]
    private Collection $catalogs;

    /** @var Collection<Distribution> */
    #[ORM\OneToMany(targetEntity: Distribution::class, mappedBy: 'dataset', cascade: ['persist'])]
    #[ORM\OrderBy(['slug' => 'ASC'])]
    private Collection $distributions;

    #[ORM\JoinColumn(name: 'study_id', referencedColumnName: 'id', nullable: true)]
    #[ORM\ManyToOne(targetEntity: Study::class, inversedBy: 'datasets')]
    private ?Study $study = null;

    /** @var Collection<DatasetMetadata> */
    #[ORM\OneToMany(targetEntity: DatasetMetadata::class, mappedBy: 'dataset')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $metadata;

    #[ORM\Column(type: 'boolean')]
    private bool $isPublished = false;

    /** @var Collection<DatasetPermission> */
    #[ORM\OneToMany(targetEntity: DatasetPermission::class, cascade: ['persist', 'remove'], orphanRemoval: true, mappedBy: 'dataset')]
    private Collection $permissions;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isArchived = false;

    #[ORM\JoinColumn(name: 'default_metadata_model_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: MetadataModel::class, inversedBy: 'datasets')]
    private ?MetadataModel $defaultMetadataModel = null;

    public function __construct(string $slug)
    {
        $this->slug = $slug;
        $this->catalogs = new ArrayCollection();
        $this->metadata = new ArrayCollection();
        $this->distributions = new ArrayCollection();
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

    /** @return Collection<string, Distribution> */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }

    /** @param Collection<string, Distribution> $distributions */
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

    /** @return Collection<DatasetPermission> */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermissionForUser(User $user, PermissionType $type): DatasetPermission
    {
        $permission = new DatasetPermission($user, $type, $this);
        $this->permissions->add($permission);

        return $permission;
    }

    public function removePermissionForUser(User $user): void
    {
        $permission = $this->getPermissionsForUser($user);
        $this->permissions->removeElement($permission);
    }

    public function getPermissionsForUser(User $user): ?DatasetPermission
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

    /** @return MetadataEnrichedEntity[] */
    public function getChildren(ResourceType $resourceType): array
    {
        if ($resourceType->isDistribution()) {
            return $this->distributions->toArray();
        }

        return [];
    }

    /** @return MetadataEnrichedEntity[] */
    public function getParents(ResourceType $resourceType): array
    {
        if ($resourceType->isFdp()) {
            return array_unique(
                array_merge(...$this->catalogs->map(static function (Catalog $catalog) {
                    return $catalog->getParents(ResourceType::fdp());
                })->toArray())
            );
        }

        if ($resourceType->isCatalog()) {
            return $this->catalogs->toArray();
        }

        if ($resourceType->isStudy()) {
            return $this->study !== null ? [$this->study] : [];
        }

        return [];
    }
}
