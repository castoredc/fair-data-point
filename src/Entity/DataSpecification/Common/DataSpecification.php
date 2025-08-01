<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Enum\PermissionType;
use App\Entity\Version as VersionNumber;
use App\Security\Permission;
use App\Security\PermissionsEnabledEntity;
use App\Security\User;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'data_specification')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['model' => 'App\Entity\DataSpecification\DataModel\DataModel', 'dictionary' => 'App\Entity\DataSpecification\DataDictionary\DataDictionary', 'metadata_model' => 'App\Entity\DataSpecification\MetadataModel\MetadataModel'])]
abstract class DataSpecification implements PermissionsEnabledEntity
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'string')]
    private string $title;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** @var Collection<DistributionContents> */
    #[ORM\OneToMany(targetEntity: DistributionContents::class, mappedBy: 'dataSpecification')]
    private Collection $distributionContents;

    /** @var Collection<Version> */
    #[ORM\OneToMany(targetEntity: Version::class, mappedBy: 'dataSpecification', cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $versions;

    #[ORM\Column(type: 'boolean', options: ['default' => 0])]
    private bool $isPublic = false;

    /** @var Collection<DataSpecificationPermission> */
    #[ORM\OneToMany(targetEntity: DataSpecificationPermission::class, cascade: ['persist', 'remove'], orphanRemoval: true, mappedBy: 'dataSpecification')]
    private Collection $permissions;

    public function __construct(string $title, ?string $description)
    {
        $this->title = $title;
        $this->description = $description;

        $this->distributionContents = new ArrayCollection();
        $this->versions = new ArrayCollection();
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /** @return Collection<DistributionContents> */
    public function getDistributionContents(): Collection
    {
        return $this->distributionContents;
    }

    /** @return Collection<Version> */
    public function getVersions(): Collection
    {
        return $this->versions;
    }

    public function addVersion(Version $version): void
    {
        $version->setDataSpecification($this);
        $this->versions->add($version);
    }

    public function getLatestVersion(): Version
    {
        return $this->versions->last();
    }

    public function hasVersion(VersionNumber $version): bool
    {
        foreach ($this->versions as $dataSpecificationVersion) {
            if ($dataSpecificationVersion->getVersion() === $version) {
                return true;
            }
        }

        return false;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    /** @return Collection<DataSpecificationPermission> */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermissionForUser(User $user, PermissionType $type): Permission
    {
        $permission = new DataSpecificationPermission($user, $type, $this);
        $this->permissions->add($permission);

        return $permission;
    }

    public function removePermissionForUser(User $user): void
    {
        $permission = $this->getPermissionsForUser($user);
        $this->permissions->removeElement($permission);
    }

    public function getPermissionsForUser(User $user): ?DataSpecificationPermission
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
            PermissionType::accessData(),
            PermissionType::view(),
            PermissionType::edit(),
            PermissionType::manage(),
        ];
    }
}
