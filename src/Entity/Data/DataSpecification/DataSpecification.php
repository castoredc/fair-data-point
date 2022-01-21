<?php
declare(strict_types=1);

namespace App\Entity\Data\DataSpecification;

use App\Entity\Data\DistributionContents\DistributionContents;
use App\Entity\Version as VersionNumber;
use App\Security\User;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="data_specification")
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *     "model" = "App\Entity\Data\DataModel\DataModel",
 *     "dictionary" = "App\Entity\Data\DataDictionary\DataDictionary",
 * })
 */
abstract class DataSpecification
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="string") */
    private string $title;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $description = null;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DistributionContents\DistributionContents", mappedBy="dataSpecification")
     *
     * @var Collection<DistributionContents>
     */
    private Collection $distributionContents;

    /**
     * @ORM\OneToMany(targetEntity="Version", mappedBy="dataSpecification", cascade={"persist"}, fetch="EAGER")
     * @ORM\OrderBy({"createdAt" = "ASC"})
     *
     * @var Collection<Version>
     */
    private Collection $versions;

    /** @ORM\Column(type="boolean") */
    private bool $isPublic = false;

    /**
     * @ORM\OneToMany(targetEntity="DataSpecificationPermission", mappedBy="dataSpecification")
     *
     * @var Collection<DataSpecificationPermission>
     */
    private Collection $permissions;

    public function __construct(string $title, ?string $description)
    {
        $this->title = $title;
        $this->description = $description;

        $this->distributionContents = new ArrayCollection();
        $this->versions = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
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

    /**
     * @return Collection<DistributionContents>
     */
    public function getDistributionContents(): Collection
    {
        return $this->distributionContents;
    }

    /**
     * @return Collection<Version>
     */
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

    /**
     * @return Collection<DataSpecificationPermission>
     */
    public function getPermissions(): Collection
    {
        return $this->permissions;
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
}
