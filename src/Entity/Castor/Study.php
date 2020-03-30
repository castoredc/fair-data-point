<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\FAIRData\Dataset;
use App\Entity\Metadata\StudyMetadata;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;
use function count;

/**
 * @ORM\Entity
 * @ORM\Table(name="study", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
class Study implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=190)
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $name;

    /** @var string|null */
    private $mainAgent;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $slug;

    /** @var ArrayCollection<string, Field>|null */
    private $fields;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\StudyMetadata", mappedBy="study",cascade={"persist"}, fetch = "EAGER")
     *
     * @var StudyMetadata[]|ArrayCollection
     */
    private $metadata;

    /**
     * One Customer has One Cart.
     *
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Dataset", inversedBy="study")
     *
     * @var Dataset|null
     */
    private $dataset;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $enteredManually = false;

    /**
     * @param ArrayCollection<string, Field>|null $fields
     */
    public function __construct(?string $id, ?string $name, ?string $mainAgent, ?string $slug, ?ArrayCollection $fields)
    {
        $this->id = $id;
        $this->name = $name;
        $this->mainAgent = $mainAgent;
        $this->slug = $slug;
        $this->fields = $fields;
        $this->metadata = new ArrayCollection();
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getMainAgent(): ?string
    {
        return $this->mainAgent;
    }

    public function setMainAgent(?string $mainAgent): void
    {
        $this->mainAgent = $mainAgent;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(?string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return ArrayCollection<string, Field>|null
     */
    public function getFields(): ?ArrayCollection
    {
        return $this->fields;
    }

    /**
     * @param ArrayCollection<string, Field>|null $fields
     */
    public function setFields(?ArrayCollection $fields): void
    {
        $this->fields = $fields;
    }

    /**
     * @return StudyMetadata[]|ArrayCollection
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    public function getLatestMetadata(): ?StudyMetadata
    {
        return $this->metadata->isEmpty() ? null : $this->metadata->last();
    }

    public function getLatestMetadataVersion(): int
    {
        return $this->metadata->count();
    }

    public function hasMetadata(): bool
    {
        return count($this->metadata) > 0;
    }

    /**
     * @param StudyMetadata[]|ArrayCollection $metadata
     */
    public function setMetadata($metadata): void
    {
        $this->metadata = $metadata;
    }

    public function addMetadata(StudyMetadata $metadata): void
    {
        $this->metadata[] = $metadata;
    }

    public function getDataset(): ?Dataset
    {
        return $this->dataset;
    }

    public function setDataset(?Dataset $dataset): void
    {
        $this->dataset = $dataset;
    }

    public function isEnteredManually(): bool
    {
        return $this->enteredManually;
    }

    public function setEnteredManually(bool $enteredManually): void
    {
        $this->enteredManually = $enteredManually;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Study
    {
        return new Study(
            $data['study_id'] ?? null,
            $data['name'] ?? null,
            $data['main_contact'] ?? null,
            $data['slug'] ?? null,
            null
        );
    }

    /**
     * @return array<mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
        ];
    }
}
