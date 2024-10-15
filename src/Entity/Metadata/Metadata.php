<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\DataSpecification\MetadataModel\MetadataModelVersion;
use App\Entity\DataSpecification\MetadataModel\Node\ValueNode;
use App\Entity\Enum\ResourceType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Iri;
use App\Entity\Version;
use App\Exception\NotFound;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use function assert;
use function json_decode;

#[ORM\Table(name: 'metadata')]
#[ORM\Entity]
#[ORM\InheritanceType('JOINED')]
#[ORM\HasLifecycleCallbacks]
abstract class Metadata
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'title', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: LocalizedText::class, cascade: ['persist'])]
    private ?LocalizedText $title = null;

    #[ORM\Column(type: 'version')]
    private Version $version;

    /** @var Collection<MetadataValue> */
    #[ORM\OneToMany(targetEntity: MetadataValue::class, mappedBy: 'metadata')]
    protected Collection $values;

    #[ORM\JoinColumn(name: 'description', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: LocalizedText::class, cascade: ['persist'])]
    private ?LocalizedText $description = null;

    #[ORM\JoinColumn(name: 'language', referencedColumnName: 'code')]
    #[ORM\ManyToOne(targetEntity: Language::class, cascade: ['persist'])]
    private ?Language $language = null;

    #[ORM\JoinColumn(name: 'license', referencedColumnName: 'slug', nullable: true)]
    #[ORM\ManyToOne(targetEntity: License::class, cascade: ['persist'])]
    private ?License $license = null;

    /** @var Collection<Agent> */
    #[ORM\JoinTable(name: 'metadata_publishers')]
    #[ORM\ManyToMany(targetEntity: Agent::class, cascade: ['persist'])]
    private Collection $publishers;

    /** @var Collection<Agent> */
    #[ORM\JoinTable(name: 'dataset_contacts')]
    #[ORM\ManyToMany(targetEntity: Agent::class, cascade: ['persist'])]
    private Collection $contacts;

    #[ORM\Column(type: 'iri', nullable: true)]
    private ?Iri $landingPage = null;

    #[ORM\JoinColumn(name: 'metadata_model_version_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: MetadataModelVersion::class, inversedBy: 'assignedMetadata')]
    private ?MetadataModelVersion $metadataModelVersion;

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getLegacyTitle(): ?LocalizedText
    {
        return $this->title;
    }

    public function getLegacyDescription(): ?LocalizedText
    {
        return $this->description;
    }

    public function setTitle(?LocalizedText $title): void
    {
        $this->title = $title;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    public function getLicense(): ?License
    {
        return $this->license;
    }

    public function setLicense(?License $license): void
    {
        $this->license = $license;
    }

    /** @return Collection<Agent> */
    public function getPublishers(): Collection
    {
        return $this->publishers;
    }

    /** @param Collection<Agent> $publishers */
    public function setPublishers(Collection $publishers): void
    {
        $this->publishers = $publishers;
    }

    /** @return Collection<Agent> */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /** @param Collection<Agent> $contacts */
    public function setContacts(Collection $contacts): void
    {
        $this->contacts = $contacts;
    }

    public function getLandingPage(): ?Iri
    {
        return $this->landingPage;
    }

    public function setLandingPage(?Iri $landingPage): void
    {
        $this->landingPage = $landingPage;
    }

    public function getMetadataModelVersion(): ?MetadataModelVersion
    {
        return $this->metadataModelVersion;
    }

    public function setMetadataModelVersion(?MetadataModelVersion $metadataModelVersion): void
    {
        $this->metadataModelVersion = $metadataModelVersion;
    }

    public function getEntity(): ?MetadataEnrichedEntity
    {
        return null;
    }

    public function getTitle(): ?LocalizedText
    {
        $modelVersion = $this->metadataModelVersion;

        if ($modelVersion === null) {
            return null;
        }

        $resourceType = $this->getResourceType();

        $value = $this->values->findFirst(static function (int $key, MetadataValue $value) use ($modelVersion, $resourceType) {
            return $modelVersion->getTitleNode($resourceType) === $value->getNode();
        });
        assert($value instanceof MetadataValue || $value === null);

        return $value !== null ? LocalizedText::fromArray(json_decode($value->getValue(), true)) : null;
    }

    public function getDescription(): ?LocalizedText
    {
        $modelVersion = $this->metadataModelVersion;

        if ($modelVersion === null) {
            return null;
        }

        $resourceType = $this->getResourceType();

        $value = $this->values->findFirst(static function (int $key, MetadataValue $value) use ($modelVersion, $resourceType) {
            return $modelVersion->getDescriptionNode($resourceType) === $value->getNode();
        });
        assert($value instanceof MetadataValue || $value === null);

        return $value !== null ? LocalizedText::fromArray(json_decode($value->getValue(), true)) : null;
    }

    /** @return Collection<MetadataValue> */
    public function getValues(): Collection
    {
        return $this->values;
    }

    public function getValueForNode(ValueNode $node): ?MetadataValue
    {
        return $this->values->findFirst(static function (int $key, MetadataValue $value) use ($node) {
            return $value->getNode() === $node;
        });
    }

    public function addValue(MetadataValue $value): void
    {
        $this->values->add($value);
    }

    public function removeValue(MetadataValue $value): void
    {
        $this->values->removeElement($value);
    }

    public function getResourceType(): ResourceType
    {
        throw new NotFound();
    }
}
