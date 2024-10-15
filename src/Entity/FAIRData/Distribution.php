<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Connection\DistributionDatabaseInformation;
use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Enum\PermissionType;
use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Permission\DistributionPermission;
use App\Entity\Metadata\DistributionMetadata;
use App\Entity\Study;
use App\Entity\Version;
use App\Security\ApiUser;
use App\Security\PermissionsEnabledEntity;
use App\Security\User;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use function count;

#[ORM\Table(name: 'distribution')]
#[ORM\Index(name: 'slug', columns: ['slug'])]
#[ORM\Entity(repositoryClass: \App\Repository\DistributionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Distribution implements AccessibleEntity, MetadataEnrichedEntity, PermissionsEnabledEntity
{
    use CreatedAndUpdated;

    public const URL_PATH = '/fdp/distribution/';

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $slug;
    #[ORM\JoinColumn(name: 'dataset_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FAIRData\Dataset::class, inversedBy: 'distributions', cascade: ['persist'])]
    private ?Dataset $dataset = null;

    #[ORM\OneToOne(targetEntity: \App\Entity\Data\DistributionContents\DistributionContents::class, mappedBy: 'distribution')]
    private ?DistributionContents $contents = null;

    #[ORM\OneToOne(targetEntity: \App\Entity\Connection\DistributionDatabaseInformation::class, mappedBy: 'distribution')]
    private ?DistributionDatabaseInformation $databaseInformation = null;

    #[ORM\JoinColumn(name: 'license', referencedColumnName: 'slug', nullable: true)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FAIRData\License::class, cascade: ['persist'])]
    private ?License $license = null;

    /**
     *
     * @var Collection<DistributionMetadata>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Metadata\DistributionMetadata::class, mappedBy: 'distribution')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $metadata;

    #[ORM\JoinColumn(name: 'user_api', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Security\ApiUser::class)]
    private ?ApiUser $apiUser = null;

    /**
     *
     * @var Collection<Agent>
     */
    #[ORM\JoinTable(name: 'distribution_contactpoint')]
    #[ORM\ManyToMany(targetEntity: \App\Entity\FAIRData\Agent\Agent::class, cascade: ['persist'])]
    private Collection $contactPoints;

    /**
     * @var Collection<DistributionPermission>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\FAIRData\Permission\DistributionPermission::class, cascade: ['persist', 'remove'], orphanRemoval: true, mappedBy: 'distribution')]
    private Collection $permissions;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isPublished = false;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isArchived = false;

    #[ORM\JoinColumn(name: 'default_metadata_model_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\DataSpecification\MetadataModel\MetadataModel::class, inversedBy: 'distributions')]
    private ?MetadataModel $defaultMetadataModel = null;

    public function __construct(string $slug, Dataset $dataset)
    {
        $this->slug = $slug;
        $this->dataset = $dataset;
        $this->metadata = new ArrayCollection();
        $this->contactPoints = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
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

    /** @return Collection<Agent> */
    public function getContactPoints(): Collection
    {
        return $this->contactPoints;
    }

    /** @param Collection<Agent> $contactPoints */
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

    /** @return Collection<DistributionPermission> */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermissionForUser(User $user, PermissionType $type): DistributionPermission
    {
        $permission = new DistributionPermission($user, $type, $this);
        $this->permissions->add($permission);

        return $permission;
    }

    public function removePermissionForUser(User $user): void
    {
        $permission = $this->getPermissionsForUser($user);
        $this->permissions->removeElement($permission);
    }

    public function getPermissionsForUser(User $user): ?DistributionPermission
    {
        foreach ($this->permissions->toArray() as $permission) {
            if ($permission->getUser() === $user) {
                return $permission;
            }
        }

        return null;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
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

    public function getAccessUrl(): ?string
    {
        return $this->contents->getRelativeUrl();
    }

    public function getMediaType(): ?string
    {
        return $this->contents->getMediaType();
    }

    /** @return MetadataEnrichedEntity[] */
    public function getChildren(ResourceType $resourceType): array
    {
        return [];
    }

    /** @return MetadataEnrichedEntity[] */
    public function getParents(ResourceType $resourceType): array
    {
        if ($resourceType->isFdp()) {
            return $this->dataset->getParents(ResourceType::fdp());
        }

        if ($resourceType->isCatalog()) {
            return $this->dataset->getParents(ResourceType::catalog());
        }

        if ($resourceType->isStudy()) {
            return $this->dataset->getParents(ResourceType::study());
        }

        if ($resourceType->isDataset()) {
            return [$this->dataset];
        }

        return [];
    }
}
