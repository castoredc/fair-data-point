<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Mapping\ElementMapping;
use App\Entity\DataSpecification\Common\Mapping\GroupMapping;
use App\Entity\DataSpecification\Common\Mapping\Mapping;
use App\Entity\DataSpecification\Common\Version as DataSpecificationVersion;
use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\StudySource;
use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Distribution;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Metadata\StudyMetadata;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use function array_merge;
use function array_unique;
use function count;

#[ORM\Table(name: 'study')]
#[ORM\Index(name: 'slug', columns: ['slug'])]
#[ORM\Entity(repositoryClass: \App\Repository\StudyRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
#[ORM\DiscriminatorColumn(name: 'type', type: 'string')]
#[ORM\DiscriminatorMap(['castor' => 'App\Entity\Castor\CastorStudy'])]
abstract class Study implements AccessibleEntity, MetadataEnrichedEntity
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string|null $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $sourceId = null;

    #[ORM\Column(type: 'StudySource', nullable: true)]
    private ?StudySource $source = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $slug;

    /**
     * @var Collection<StudyMetadata>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\Metadata\StudyMetadata::class, mappedBy: 'study', cascade: ['persist'], fetch: 'EAGER')]
    private Collection $metadata;

    /**
     * @var Collection<Dataset>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\FAIRData\Dataset::class, mappedBy: 'study', fetch: 'EAGER')]
    private Collection $datasets;

    #[ORM\Column(type: 'boolean')]
    private bool $enteredManually = false;

    /**
     * @var Collection<Catalog>
     */
    #[ORM\ManyToMany(targetEntity: \App\Entity\FAIRData\Catalog::class, mappedBy: 'studies', cascade: ['persist'])]
    private Collection $catalogs;

    #[ORM\Column(type: 'boolean')]
    private bool $isPublished = false;

    /**
     * @var Collection<Mapping>
     */
    #[ORM\OneToMany(targetEntity: \App\Entity\DataSpecification\Common\Mapping\Mapping::class, mappedBy: 'study', cascade: ['persist', 'remove'])]
    private Collection $mappings;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isArchived = false;

    #[ORM\JoinColumn(name: 'default_metadata_model_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: \App\Entity\DataSpecification\MetadataModel\MetadataModel::class, inversedBy: 'studies')]
    private ?MetadataModel $defaultMetadataModel = null;

    public function __construct(StudySource $source, ?string $sourceId, ?string $name, ?string $slug)
    {
        $this->source = $source;
        $this->sourceId = $sourceId;
        $this->name = $name;
        $this->slug = $slug;
        $this->metadata = new ArrayCollection();
        $this->datasets = new ArrayCollection();
        $this->catalogs = new ArrayCollection();
        $this->mappings = new ArrayCollection();
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /** @return Collection<StudyMetadata> */
    public function getMetadata(): Collection
    {
        return $this->metadata;
    }

    public function getFirstMetadata(): ?StudyMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->first();
    }

    public function getLatestMetadata(): ?StudyMetadata
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

    public function addMetadata(StudyMetadata $metadata): void
    {
        $this->metadata->add($metadata);
    }

    /** @return Collection<Dataset> */
    public function getDatasets(): Collection
    {
        return $this->datasets;
    }

    public function addDataset(Dataset $dataset): void
    {
        $this->datasets->add($dataset);
    }

    public function removeDataset(Dataset $dataset): void
    {
        $this->datasets->removeElement($dataset);
    }

    public function isEnteredManually(): bool
    {
        return $this->enteredManually;
    }

    public function setEnteredManually(bool $enteredManually): void
    {
        $this->enteredManually = $enteredManually;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    /** @return Collection<Catalog> */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    public function getSourceId(): ?string
    {
        return $this->sourceId;
    }

    public function getSource(): ?StudySource
    {
        return $this->source;
    }

    public function setSourceId(?string $sourceId): void
    {
        $this->sourceId = $sourceId;
    }

    public function getRelativeUrl(): string
    {
        return '/study/' . $this->slug;
    }

    /** @return Collection<Mapping> */
    public function getMappings(): Collection
    {
        return $this->mappings;
    }

    /** @return Collection<ElementMapping> */
    public function getNodeMappings(): Collection
    {
        $return = new ArrayCollection();

        foreach ($this->mappings as $mapping) {
            if (! $mapping instanceof ElementMapping) {
                continue;
            }

            $return->add($mapping);
        }

        return $return;
    }

    /** @return Collection<GroupMapping> */
    public function getModuleMappings(): Collection
    {
        $return = new ArrayCollection();

        foreach ($this->mappings as $mapping) {
            if (! $mapping instanceof GroupMapping) {
                continue;
            }

            $return->add($mapping);
        }

        return $return;
    }

    public function getMappingByNodeAndVersion(Element $element, DataSpecificationVersion $version): ?ElementMapping
    {
        foreach ($this->getNodeMappings() as $mapping) {
            if ($mapping->getElement() === $element && $mapping->getVersion() === $version) {
                return $mapping;
            }
        }

        return null;
    }

    public function getMappingByModuleAndVersion(Group $group, DataSpecificationVersion $version): ?GroupMapping
    {
        foreach ($this->getModuleMappings() as $mapping) {
            if ($mapping->getGroup() === $group && $mapping->getVersion() === $version) {
                return $mapping;
            }
        }

        return null;
    }

    public function isArchived(): bool
    {
        return $this->isArchived;
    }

    /** @return Distribution[] */
    public function getDistributions(): array
    {
        return array_merge(...$this->datasets->map(static function (Dataset $dataset) {
                return $dataset->getDistributions()->toArray();
        })->toArray());
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
        if ($resourceType->isDataset()) {
            return $this->datasets->toArray();
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
            return array_unique(
                array_merge(...$this->catalogs->map(static function (Catalog $catalog) {
                    return $catalog->getParents(ResourceType::fdp());
                })->toArray())
            );
        }

        if ($resourceType->isCatalog()) {
            return $this->catalogs->toArray();
        }

        return [];
    }
}
