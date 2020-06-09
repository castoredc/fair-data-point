<?php
declare(strict_types=1);

namespace App\Entity\Castor;

use App\Entity\Castor\Form\Field;
use App\Entity\FAIRData\Dataset;
use App\Entity\Metadata\StudyMetadata;
use App\Security\CastorServer;
use App\Security\CastorUser;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use function count;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudyRepository")
 * @ORM\Table(name="study", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Study
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
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\Dataset", mappedBy="study")
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
     * @ORM\ManyToOne(targetEntity="App\Security\CastorServer")
     * @ORM\JoinColumn(name="server", referencedColumnName="id")
     *
     * @var CastorServer|null
     */
    private $server;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime $created
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     *
     * @var DateTime|null $updated
     */
    protected $updated;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorUser")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     *
     * @var CastorUser|null $createdBy
     * @Gedmo\Blameable(on="create")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity="App\Security\CastorUser")
     * @ORM\JoinColumn(name="updated_by", referencedColumnName="id")
     *
     * @var CastorUser|null $updatedBy
     * @Gedmo\Blameable(on="update")
     */
    private $updatedBy;

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

    public function getServer(): ?CastorServer
    {
        return $this->server;
    }

    public function setServer(?CastorServer $server): void
    {
        $this->server = $server;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->created = new DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->updated = new DateTime('now');
    }

    public function getCreatedBy(): ?CastorUser
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?CastorUser
    {
        return $this->updatedBy;
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
}
