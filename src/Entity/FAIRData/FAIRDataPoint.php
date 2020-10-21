<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\Iri;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fdp", indexes={@ORM\Index(name="iri", columns={"iri"})})
 */
class FAIRDataPoint implements AccessibleEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="iri") */
    private Iri $iri;

    /* DC terms */

    /**
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="title", referencedColumnName="id")
     */
    private ?LocalizedText $title = null;

    /** @ORM\Column(type="string") */
    private string $version;

    /**
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="description", referencedColumnName="id")
     */
    private ?LocalizedText $description = null;

    /** @var Collection<string, Agent> */
    private Collection $publishers;

    /**
     * @ORM\ManyToOne(targetEntity="Language",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     */
    private ?Language $language = null;

    /**
     * @ORM\ManyToOne(targetEntity="License",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="license", referencedColumnName="slug", nullable=true)
     */
    private ?License $license = null;

    /**
     * @ORM\OneToMany(targetEntity="Catalog", mappedBy="fairDataPoint",cascade={"persist"}, fetch = "EAGER")
     *
     * @var Collection<string, Catalog>
     */
    private Collection $catalogs;

    /**
     * @param Collection<string, Agent> $publishers
     */
    public function __construct(Iri $iri, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license)
    {
        $this->iri = $iri;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
        $this->catalogs = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
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

    public function getRelativeUrl(): string
    {
        return '/fdp';
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

    /**
     * @return Collection<string, Catalog>
     */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    /**
     * @param Collection<string, Catalog> $catalogs
     */
    public function setCatalogs(Collection $catalogs): void
    {
        $this->catalogs = $catalogs;
    }

    public function addCatalog(Catalog $catalog): void
    {
        $this->catalogs->add($catalog);
    }
}
