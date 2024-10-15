<?php
declare(strict_types=1);

namespace App\Entity\Data\DistributionContents;

use App\Entity\Data\DistributionContents\Dependency\DependencyGroup;
use App\Entity\Data\Log\DistributionGenerationLog;
use App\Entity\DataSpecification\Common\DataSpecification;
use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Mapping\ElementMapping;
use App\Entity\DataSpecification\Common\Mapping\GroupMapping;
use App\Entity\DataSpecification\Common\Version;
use App\Entity\Enum\PermissionType;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\Permission\DistributionContentsPermission;
use App\Entity\Study;
use App\Security\PermissionsEnabledEntity;
use App\Security\User;
use App\Traits\CreatedAndUpdated;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use function assert;

#[ORM\Table(name: 'distribution_contents')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['csv' => 'CSVDistribution', 'rdf' => 'RDFDistribution'])]
abstract class DistributionContents implements PermissionsEnabledEntity
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'distribution', referencedColumnName: 'id', nullable: false)]
    #[ORM\OneToOne(targetEntity: \App\Entity\FAIRData\Distribution::class, inversedBy: 'contents')]
    private Distribution $distribution;

    #[ORM\Column(type: 'boolean')]
    private bool $isPublic = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isCached = false;

    /**
     *
     * @var Collection<DistributionGenerationLog>
     */
    #[ORM\JoinColumn(name: 'distribution', referencedColumnName: 'id')]
    #[ORM\OneToMany(targetEntity: \App\Entity\Data\Log\DistributionGenerationLog::class, mappedBy: 'distribution', cascade: ['persist'])]
    #[ORM\OrderBy(['createdAt' => 'DESC'])]
    protected Collection $logs;

    #[ORM\JoinColumn(name: 'dependencies', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: \App\Entity\Data\DistributionContents\Dependency\DependencyGroup::class, cascade: ['persist'], fetch: 'EAGER')]
    private ?DependencyGroup $dependencies = null;

    #[ORM\JoinColumn(name: 'data_specification', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\DataSpecification\Common\DataSpecification::class, inversedBy: 'distributionContents')]
    protected DataSpecification $dataSpecification;

    #[ORM\JoinColumn(name: 'data_specification_version', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\DataSpecification\Common\Version::class, inversedBy: 'distributionContents')]
    protected Version $currentDataSpecificationVersion;

    /**
     * @var Collection<DistributionContentsPermission>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\FAIRData\Permission\DistributionContentsPermission::class, cascade: ['persist', 'remove'], orphanRemoval: true, mappedBy: 'distributionContents')]
    private Collection $permissions;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
        $this->logs = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getStudy(): Study
    {
        return $this->getDistribution()->getStudy();
    }

    /** @return Collection<DistributionGenerationLog> */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    public function addLog(DistributionGenerationLog $log): void
    {
        $this->logs->add($log);
    }

    public function getLastGenerationDate(): ?DateTimeImmutable
    {
        if ($this->logs->count() === 0) {
            return null;
        }

        $firstLog = $this->logs->first();
        assert($firstLog instanceof DistributionGenerationLog);

        return $firstLog->getCreatedAt();
    }

    public function getDependencies(): ?DependencyGroup
    {
        return $this->dependencies;
    }

    public function setDependencies(?DependencyGroup $dependencies): void
    {
        $this->dependencies = $dependencies;
    }

    public function isCached(): bool
    {
        return $this->isCached;
    }

    public function setIsCached(bool $isCached): void
    {
        $this->isCached = $isCached;
    }

    public function getDataSpecification(): DataSpecification
    {
        return $this->dataSpecification;
    }

    protected function setDataSpecification(DataSpecification $dataSpecification): void
    {
        $this->dataSpecification = $dataSpecification;
    }

    protected function setCurrentDataSpecificationVersion(Version $dataSpecificationVersion): void
    {
        if ($dataSpecificationVersion->getDataSpecification() !== $this->dataSpecification) {
            return;
        }

        $this->currentDataSpecificationVersion = $dataSpecificationVersion;
    }

    public function getMappingByGroupForCurrentVersion(Group $group): ?GroupMapping
    {
        return $this->getStudy()->getMappingByModuleAndVersion($group, $this->currentDataSpecificationVersion);
    }

    public function getMappingByElementForCurrentVersion(Element $element): ?ElementMapping
    {
        return $this->getStudy()->getMappingByNodeAndVersion($element, $this->currentDataSpecificationVersion);
    }

    /** @return Collection<DistributionContentsPermission> */
    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function addPermissionForUser(User $user, PermissionType $type): DistributionContentsPermission
    {
        $permission = new DistributionContentsPermission($user, $type, $this);
        $this->permissions->add($permission);

        return $permission;
    }

    public function removePermissionForUser(User $user): void
    {
        $permission = $this->getPermissionsForUser($user);
        $this->permissions->removeElement($permission);
    }

    public function getPermissionsForUser(User $user): ?DistributionContentsPermission
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
        return [PermissionType::accessData()];
    }

    public function getType(): string
    {
        return '';
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function setIsPublic(bool $isPublic): void
    {
        $this->isPublic = $isPublic;
    }

    public function getRelativeUrl(): string
    {
        return '';
    }

    public function getMediaType(): string
    {
        return '';
    }
}
