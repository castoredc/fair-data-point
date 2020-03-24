<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\Iri;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;
use function array_merge;

//use EasyRdf_Graph;

/**
 * @ORM\Entity
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

    /* DC terms */

    /**
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"})
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
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="description", referencedColumnName="id")
     *
     * @var LocalizedText|null
     */
    private $description;

    /**
     * @var Collection<string, Agent>
     */
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
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $issued;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $modified;

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
     * @var Collection<string, Agent>
     */
    private $contactPoint;

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
     * @ORM\OneToOne(targetEntity="App\Entity\Castor\Study",cascade={"persist"})
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=true)
     *
     * @var Study|null
     */
    private $study;

    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $logo;

    /**
     * @param Collection<string, Agent> $publishers
     * @param Collection<string, Agent> $contactPoint
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified, Collection $contactPoint, ?LocalizedText $keyword, ?Iri $landingPage)
    {
        $this->slug = $slug;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
        $this->issued = $issued;
        $this->modified = $modified;
        $this->contactPoint = $contactPoint;
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

    public function getLicense(): ?License
    {
        return $this->license;
    }

    public function setLicense(?License $license): void
    {
        $this->license = $license;
    }

    public function getIssued(): DateTime
    {
        return $this->issued;
    }

    public function setIssued(DateTime $issued): void
    {
        $this->issued = $issued;
    }

    public function getModified(): DateTime
    {
        return $this->modified;
    }

    public function setModified(DateTime $modified): void
    {
        $this->modified = $modified;
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

    /**
     * @return Collection<string, Agent>
     */
    public function getContactPoint(): Collection
    {
        return $this->contactPoint;
    }

    /**
     * @param Collection<string, Agent> $contactPoint
     */
    public function setContactPoint(Collection $contactPoint): void
    {
        $this->contactPoint = $contactPoint;
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

    public function getLogo(): ?Iri
    {
        return $this->logo;
    }

    /**
     * @return array<mixed>
     */
    public function toBasicArray(): array
    {
        $publishers = [];
        foreach ($this->publishers as $publisher) {
            /** @var Agent $publisher */
            $publishers[] = $publisher->toArray();
        }

        $contactPoints = [];
        foreach ($this->contactPoint as $contactPoint) {
            /** @var Agent $contactPoint */
            $contactPoints[] = $contactPoint->toArray();
        }

        return [
            'access_url' => $this->getAccessUrl(),
            'relative_url' => $this->getRelativeUrl(),
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title->toArray(),
            'version' => $this->version,
            'description' => $this->description->toArray(),
            'publishers' => $publishers,
            'language' => $this->language->toArray(),
            'license' => $this->license->toArray(),
            'issued' => $this->issued,
            'modified' => $this->modified,
            'contactPoints' => $contactPoints,
//            'keyword' => $this->keyword->toArray(),
            'landingpage' => $this->landingPage !== null ? $this->landingPage->getValue() : '',
            'logo' => $this->logo !== null ? $this->logo->getValue() : '',
        ];
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $distributions = [];
        foreach ($this->distributions as $distribution) {
            /** @var Distribution $distribution */
            $distributions[] = $distribution->toBasicArray();
        }

        return array_merge($this->toBasicArray(), ['distributions' => $distributions]);
    }

    public function toGraph(): EasyRdf_Graph
    {
        $graph = new EasyRdf_Graph();

        $graph->addResource($this->getAccessUrl(), 'a', 'dcat:Dataset');

        foreach ($this->title->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->getAccessUrl(), 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
            $graph->addLiteral($this->getAccessUrl(), 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        }

        $graph->addLiteral($this->getAccessUrl(), 'dcterms:hasVersion', $this->version);

        foreach ($this->description->getTexts() as $text) {
            /** @var LocalizedTextItem $text */
            $graph->addLiteral($this->getAccessUrl(), 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        }

        foreach ($this->publishers as $publisher) {
            /** @var Agent $publisher */
            $publisher->addToGraph($this->getAccessUrl(), 'dcterms:publisher', $graph);
        }

        foreach ($this->contactPoint as $contactPoint) {
            /** @var Agent $contactPoint */
            $contactPoint->addToGraph($this->getAccessUrl(), 'dcat:contactPoint', $graph);
        }

        $graph->addResource($this->getAccessUrl(), 'dcterms:language', $this->language->getAccessUrl());

        $graph->addResource($this->getAccessUrl(), 'dcterms:license', $this->license->getUrl()->getValue());

        //$graph->addResource($this->getAccessUrl(), 'dcat:theme', $this->theme->getValue());

        foreach ($this->distributions as $distribution) {
            $graph->addResource($this->getAccessUrl(), 'dcat:distribution', $distribution->getAccessUrl());
        }

        return $graph;
    }

    public function hasCatalog(Catalog $find): bool
    {
        foreach ($this->catalogs as $catalog) {
            /** @var Catalog $catalog */
            if ($catalog->getId() === $find->getId()) {
                return true;
            }
        }

        return false;
    }
}
