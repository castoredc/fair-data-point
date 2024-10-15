<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\DataSpecification\MetadataModel\MetadataModel;
use App\Entity\Enum\ResourceType;
use App\Entity\Iri;
use App\Entity\Metadata\FAIRDataPointMetadata;
use App\Entity\Version;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use function array_merge;
use function count;

#[ORM\Table(name: 'fdp')]
#[ORM\Index(name: 'iri', columns: ['iri'])]
#[ORM\Entity]
class FAIRDataPoint implements AccessibleEntity, MetadataEnrichedEntity
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'iri')]
    private Iri $iri;

    #[ORM\Column(type: 'iri', nullable: true)]
    private ?Iri $purl;

    /** @var Collection<FAIRDataPointMetadata> */
    #[ORM\OneToMany(targetEntity: FAIRDataPointMetadata::class, mappedBy: 'fdp')]
    #[ORM\OrderBy(['createdAt' => 'ASC'])]
    private Collection $metadata;

    /** @var Collection<string, Catalog> */
    #[ORM\OneToMany(targetEntity: \Catalog::class, mappedBy: 'fairDataPoint', cascade: ['persist'], fetch: 'EAGER')]
    private Collection $catalogs;

    #[ORM\Column(type: 'boolean', options: ['default' => '0'])]
    private bool $isArchived = false;

    #[ORM\JoinColumn(name: 'default_metadata_model_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: MetadataModel::class, inversedBy: 'fdps')]
    private ?MetadataModel $defaultMetadataModel = null;

    public function __construct()
    {
        $this->metadata = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getIri(): Iri
    {
        return $this->iri;
    }

    public function setIri(Iri $iri): void
    {
        $this->iri = $iri;
    }

    public function getPurl(): ?Iri
    {
        return $this->purl;
    }

    public function setPurl(?Iri $purl): void
    {
        $this->purl = $purl;
    }

    public function getRelativeUrl(): string
    {
        return '/fdp';
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

    public function addCatalog(Catalog $catalog): void
    {
        $this->catalogs->add($catalog);
    }

    public function getFirstMetadata(): ?FAIRDataPointMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->first();
    }

    public function getLatestMetadata(): ?FAIRDataPointMetadata
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

    public function addMetadata(FAIRDataPointMetadata $metadata): void
    {
        $this->metadata->add($metadata);
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

    public function getSlug(): string
    {
        return 'fdp';
    }

    /** @return MetadataEnrichedEntity[] */
    public function getChildren(ResourceType $resourceType): array
    {
        if ($resourceType->isCatalog()) {
            return $this->catalogs->toArray();
        }

        if ($resourceType->isDataset()) {
            return array_merge(...$this->catalogs->map(static function (Catalog $catalog) {
                return $catalog->getChildren(ResourceType::dataset());
            })->toArray());
        }

        if ($resourceType->isStudy()) {
            return array_merge(...$this->catalogs->map(static function (Catalog $catalog) {
                return $catalog->getChildren(ResourceType::study());
            })->toArray());
        }

        if ($resourceType->isDistribution()) {
            return array_merge(...$this->catalogs->map(static function (Catalog $catalog) {
                return $catalog->getChildren(ResourceType::distribution());
            })->toArray());
        }

        return [];
    }

    /** @return MetadataEnrichedEntity[] */
    public function getParents(ResourceType $resourceType): array
    {
        return [];
    }
}
