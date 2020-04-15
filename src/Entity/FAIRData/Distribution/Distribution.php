<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Distribution;

use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Dataset;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Security\CastorUser;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="distribution", indexes={@ORM\Index(name="slug", columns={"slug"})})
 * @ORM\HasLifecycleCallbacks
 */
class Distribution
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $slug;

    /* DC terms */

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="title", referencedColumnName="id")
     *
     * @var LocalizedText|null
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $version;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="description", referencedColumnName="id")
     *
     * @var LocalizedText|null
     */
    private $description;

    /** @var Collection<string, Agent> */
    private $publishers;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Language",cascade={"persist"})
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     *
     * @var Language|null
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\License",cascade={"persist"})
     * @ORM\JoinColumn(name="license", referencedColumnName="slug", nullable=true)
     *
     * @var License|null
     */
    private $license;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Dataset", inversedBy="distributions",cascade={"persist"})
     *
     * @var Dataset|null
     */
    private $dataset;

    /**
     * @ORM\Column(name="access", type="DistributionAccessType", nullable=false)
     *
     * @DoctrineAssert\Enum(entity="App\Type\DistributionAccessType")
     * @var int
     */
    private $accessRights;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isPublished = false;

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
     * @param Collection<string, Agent> $publishers
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license, int $accessRights, Dataset $dataset)
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
        $this->accessRights = $accessRights;
        $this->dataset = $dataset;
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getTitle(): LocalizedText
    {
        return $this->title;
    }

    public function setTitle(LocalizedText $title): void
    {
        $this->title = $title;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getDescription(): LocalizedText
    {
        return $this->description;
    }

    public function setDescription(LocalizedText $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Collection<string, Agent>
     */
    public function getPublishers(): Collection
    {
        return $this->publishers;
    }

    /**
     * @param Collection<string, Agent> $publishers
     */
    public function setPublishers(Collection $publishers): void
    {
        $this->publishers = $publishers;
    }

    public function getLanguage(): Language
    {
        return $this->language;
    }

    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    public function getLicense(): License
    {
        return $this->license;
    }

    public function setLicense(License $license): void
    {
        $this->license = $license;
    }

    public function getDataset(): Dataset
    {
        return $this->dataset;
    }

    public function setDataset(Dataset $dataset): void
    {
        $this->dataset = $dataset;
    }

    public function getAccessUrl(): string
    {
        return $this->dataset->getAccessUrl() . '/' . $this->slug;
    }

    public function getRelativeUrl(): string
    {
        return $this->dataset->getRelativeUrl() . '/' . $this->slug;
    }

    public function getBaseUrl(): string
    {
        return $this->dataset->getBaseUrl();
    }

    public function setAccessRights(int $accessRights): void
    {
        $this->accessRights = $accessRights;
    }

    public function getAccessRights(): int
    {
        return $this->accessRights;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
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

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function getCreatedBy(): ?CastorUser
    {
        return $this->createdBy;
    }

    public function getUpdatedBy(): ?CastorUser
    {
        return $this->updatedBy;
    }
}
