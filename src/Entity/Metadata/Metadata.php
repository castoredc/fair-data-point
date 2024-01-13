<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\Iri;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="metadata")
 * @ORM\HasLifecycleCallbacks
 */
abstract class Metadata
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="title", referencedColumnName="id")
     */
    private ?LocalizedText $title = null;

    /** @ORM\Column(type="version") */
    private Version $version;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="description", referencedColumnName="id")
     */
    private ?LocalizedText $description = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Language",cascade={"persist"})
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     */
    private ?Language $language = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\License",cascade={"persist"})
     * @ORM\JoinColumn(name="license", referencedColumnName="slug", nullable=true)
     */
    private ?License $license = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="metadata_publishers")
     *
     * @var Collection<Agent>
     */
    private Collection $publishers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="dataset_contacts")
     *
     * @var Collection<Agent>
     */
    private Collection $contacts;

    /** @ORM\Column(type="iri", nullable=true) */
    private ?Iri $landingPage = null;

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getTitle(): ?LocalizedText
    {
        return $this->title;
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

    public function getDescription(): ?LocalizedText
    {
        return $this->description;
    }

    public function setDescription(?LocalizedText $description): void
    {
        $this->description = $description;
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
}
