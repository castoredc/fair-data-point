<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Form\Field;
use App\Entity\FAIRData\Dataset;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Version;
use App\Security\CastorServer;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function count;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudyRepository")
 * @ORM\Table(name="study", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Study
{
    use CreatedAndUpdated;

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

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $slug;

    /** @var ArrayCollection<string, Field>|null */
    private $fields;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Metadata\StudyMetadata", mappedBy="study", cascade={"persist"}, fetch = "EAGER")
     *
     * @var StudyMetadata[]|ArrayCollection
     */
    private $metadata;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Dataset", mappedBy="study", fetch = "EAGER")
     *
     * @var Collection<Dataset>
     */
    private $datasets;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $enteredManually = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorServer")
     * @ORM\JoinColumn(name="server", referencedColumnName="id")
     *
     * @var CastorServer|null
     */
    private $server;

    /**
     * @param ArrayCollection<string, Field>|null $fields
     */
    public function __construct(?string $id, ?string $name, ?string $slug, ?ArrayCollection $fields)
    {
        $this->id = $id;
        $this->name = $name;
        $this->slug = $slug;
        $this->fields = $fields;
        $this->metadata = new ArrayCollection();
        $this->datasets = new ArrayCollection();
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

    public function getServer(): ?CastorServer
    {
        return $this->server;
    }

    public function setServer(?CastorServer $server): void
    {
        $this->server = $server;
    }

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): Study
    {
        return new Study(
            $data['study_id'] ?? null,
            $data['name'] ?? null,
            $data['slug'] ?? null,
            null
        );
    }
}
