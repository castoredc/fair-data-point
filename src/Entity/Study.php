<?php
declare(strict_types=1);

namespace App\Entity;

use App\Entity\DataSpecification\Common\Element;
use App\Entity\DataSpecification\Common\Group;
use App\Entity\DataSpecification\Common\Mapping\ElementMapping;
use App\Entity\DataSpecification\Common\Mapping\GroupMapping;
use App\Entity\DataSpecification\Common\Mapping\Mapping;
use App\Entity\DataSpecification\Common\Version as DataSpecificationVersion;
use App\Entity\Enum\StudySource;
use App\Entity\FAIRData\AccessibleEntity;
use App\Entity\FAIRData\Catalog;
use App\Entity\FAIRData\Dataset;
use App\Entity\Metadata\StudyMetadata;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;
use function count;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudyRepository")
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="study", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"castor" = "App\Entity\Castor\CastorStudy"})
 */
abstract class Study implements AccessibleEntity
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string|null $id = null;

    /** @ORM\Column(type="string", length=255, nullable=TRUE) */
    private ?string $sourceId = null;

    /** @ORM\Column(type="StudySource", nullable=TRUE) */
    private ?StudySource $source = null;

    /** @ORM\Column(type="string", length=255) */
    private string $name;

    /** @ORM\Column(type="string", length=255, unique=true) */
    private string $slug;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\StudyMetadata", mappedBy="study", cascade={"persist"}, fetch = "EAGER")
     *
     * @var Collection<StudyMetadata>
     */
    private Collection $metadata;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Dataset", mappedBy="study", fetch = "EAGER")
     *
     * @var Collection<Dataset>
     */
    private Collection $datasets;

    /** @ORM\Column(type="boolean") */
    private bool $enteredManually = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Catalog", mappedBy="studies", cascade={"persist"})
     *
     * @var Collection<Catalog>
     */
    private Collection $catalogs;

    /** @ORM\Column(type="boolean") */
    private bool $isPublished = false;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Data\DataSpecification\Mapping\Mapping", mappedBy="study", cascade={"persist", "remove"})
     *
     * @var Collection<Mapping>
     */
    private Collection $mappings;

    /** @ORM\Column(type="boolean", options={"default":"0"}) */
    private bool $isArchived = false;

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

    public function getId(): ?string
    {
        return (string) $this->id;
    }

    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
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

    public function getLatestMetadataVersion(): Version
    {
        return $this->metadata->last()->getVersion();
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
}
