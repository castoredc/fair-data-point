<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\Iri;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

//use EasyRdf_Graph;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DatasetRepository")
 * @ORM\Table(name="dataset", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
class Dataset
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

    /** @var Collection<string, Agent> */
    private $publishers;

    /**
     * @ORM\ManyToOne(targetEntity="Language",cascade={"persist"})
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     *
     * @var Language|null
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="License",cascade={"persist"})
     * @ORM\JoinColumn(name="license", referencedColumnName="slug", nullable=true)
     *
     * @var License|null
     */
    private $license;

    /**
     * @ORM\ManyToMany(targetEntity="Catalog", mappedBy="datasets",cascade={"persist"})
     *
     * @var Collection<string, Catalog>
     */
    private $catalogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\FAIRData\Distribution\Distribution", mappedBy="dataset",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Collection<string, Distribution>
     */
    private $distributions;

    /**
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="keyword", referencedColumnName="id")
     *
     * @var LocalizedText|null
     */
    private $keyword;

    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $landingPage;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Castor\Study",cascade={"persist"}, inversedBy="dataset")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=true)
     *
     * @var Study|null
     */
    private $study;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $isPublished = false;

    /**
     * @param Collection<string, Agent> $publishers
     */
    public function __construct(string $slug, Collection $publishers, Language $language, ?License $license, ?LocalizedText $keyword, ?Iri $landingPage)
    {
        $this->slug = $slug;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
        $this->keyword = $keyword;
        $this->landingPage = $landingPage;
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

    public function getLicense(): ?License
    {
        return $this->license;
    }

    public function setLicense(?License $license): void
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

    /**
     * @return Collection<string, Distribution>
     */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }

    /**
     * @param Collection<string, Distribution> $distributions
     */
    public function setDistributions(Collection $distributions): void
    {
        $this->distributions = $distributions;
    }

    public function getKeyword(): LocalizedText
    {
        return $this->keyword;
    }

    public function setKeyword(LocalizedText $keyword): void
    {
        $this->keyword = $keyword;
    }

    public function getLandingPage(): ?Iri
    {
        return $this->landingPage;
    }

    public function setLandingPage(?Iri $landingPage): void
    {
        $this->landingPage = $landingPage;
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(?Study $study): void
    {
        $this->study = $study;
    }

    public function addDistribution(Distribution $distribution): void
    {
        $this->distributions[] = $distribution;
    }

    public function getAccessUrl(): string
    {
        $first = $this->catalogs->first();

        if ($first === false) {
            return '';
        }

        return $first->getAccessUrl() . '/' . $this->slug;
    }

    public function getRelativeUrl(): string
    {
        $first = $this->catalogs->first();

        if ($first === false) {
            return '';
        }

        return $first->getRelativeUrl() . '/' . $this->slug;
    }

    public function getBaseUrl(): string
    {
        $first = $this->catalogs->first();

        if ($first === false) {
            return '';
        }

        return $first->getBaseUrl();
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): void
    {
        $this->isPublished = $isPublished;
    }

    public function hasCatalog(Catalog $catalog): bool
    {
        return $this->catalogs->contains($catalog);
    }

    public function hasDistribution(Distribution $distribution): bool
    {
        return $this->distributions->contains($distribution);
    }
}
