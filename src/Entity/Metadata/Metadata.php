<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Language;
use App\Entity\FAIRData\License;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\Iri;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="title", referencedColumnName="id")
     *
     * @var LocalizedText|null
     */
    private $title;

    /**
     * @ORM\Column(type="version")
     *
     * @var Version
     */
    private $version;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="description", referencedColumnName="id")
     *
     * @var LocalizedText|null
     */
    private $description;

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
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="dataset_publishers")
     *
     * @var Collection<Agent>
     */
    private $publishers;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\FAIRData\LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="keyword", referencedColumnName="id")
     *
     * @var LocalizedText|null
     */
    private $keyword;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="dataset_contacts")
     *
     * @var Collection<Agent>
     */
    private $contacts;

    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $landingPage;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return LocalizedText|null
     */
    public function getTitle(): ?LocalizedText
    {
        return $this->title;
    }

    /**
     * @param LocalizedText|null $title
     */
    public function setTitle(?LocalizedText $title): void
    {
        $this->title = $title;
    }

    /**
     * @return Version
     */
    public function getVersion(): Version
    {
        return $this->version;
    }

    /**
     * @param Version $version
     */
    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    /**
     * @return LocalizedText|null
     */
    public function getDescription(): ?LocalizedText
    {
        return $this->description;
    }

    /**
     * @param LocalizedText|null $description
     */
    public function setDescription(?LocalizedText $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Language|null
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language|null $language
     */
    public function setLanguage(?Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return License|null
     */
    public function getLicense(): ?License
    {
        return $this->license;
    }

    /**
     * @param License|null $license
     */
    public function setLicense(?License $license): void
    {
        $this->license = $license;
    }

    /**
     * @return Collection
     */
    public function getPublishers(): Collection
    {
        return $this->publishers;
    }

    /**
     * @param Collection $publishers
     */
    public function setPublishers(Collection $publishers): void
    {
        $this->publishers = $publishers;
    }

    /**
     * @return LocalizedText|null
     */
    public function getKeyword(): ?LocalizedText
    {
        return $this->keyword;
    }

    /**
     * @param LocalizedText|null $keyword
     */
    public function setKeyword(?LocalizedText $keyword): void
    {
        $this->keyword = $keyword;
    }

    /**
     * @return Collection
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * @param Collection $contacts
     */
    public function setContacts(Collection $contacts): void
    {
        $this->contacts = $contacts;
    }

    /**
     * @return Iri|null
     */
    public function getLandingPage(): ?Iri
    {
        return $this->landingPage;
    }

    /**
     * @param Iri|null $landingPage
     */
    public function setLandingPage(?Iri $landingPage): void
    {
        $this->landingPage = $landingPage;
    }
}
