<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 20/05/2019
 * Time: 23:50
 */

namespace App\Entity\FAIRData;


use App\Entity\Castor\Study;
use App\Entity\Iri;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;

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
     * @var LocalizedText
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
     * @var LocalizedText
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Agent", inversedBy="publishedDatasets",cascade={"persist"})
     * @ORM\JoinTable(name="datasets_publishers")
     *
     * @var Collection
     */
    private $publishers;

    /**
     * @ORM\ManyToOne(targetEntity="Language",cascade={"persist"})
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     *
     * @var Language
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

//    /**
//     * The specification of the dataset metadata schema (for example ShEx)
//     *
//     * @var Iri|null
//     */
//    private $conformsTo;
//
//    /** @var Iri|null */
//    private $rights;
//
//    /** @var Iri|null */
//    private $references;

    /**
     * @ORM\ManyToMany(targetEntity="Catalog", mappedBy="datasets",cascade={"persist"})
     *
     * @var Collection
     */
    private $catalogs;

    /**
     * @ORM\OneToMany(targetEntity="Distribution", mappedBy="dataset",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Collection
     */
    private $distributions;

//    /**
//     * List of concepts that describe the dataset
//     *
//     * @var Iri
//     */
//    private $theme;

    /**
     * @ORM\ManyToMany(targetEntity="Agent", inversedBy="contactDatasets",cascade={"persist"})
     * @ORM\JoinTable(name="datasets_contactpoints")
     *
     * @var Collection
     */
    private $contactPoint;

    /**
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"})
     * @ORM\JoinColumn(name="keyword", referencedColumnName="id")
     *
     * @var LocalizedText
     */
    private $keyword;

    /**
     * @ORM\Column(type="string", nullable=true)
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
     * Dataset constructor.
     * @param string $slug
     * @param LocalizedText $title
     * @param string $version
     * @param LocalizedText $description
     * @param Collection $publishers
     * @param Language $language
     * @param License|null $license
     * @param DateTime $issued
     * @param DateTime $modified
     * @param Collection $contactPoint
     * @param LocalizedText $keyword
     * @param Iri|null $landingPage
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

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return LocalizedText
     */
    public function getTitle(): LocalizedText
    {
        return $this->title;
    }

    /**
     * @param LocalizedText $title
     */
    public function setTitle(LocalizedText $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    /**
     * @return LocalizedText
     */
    public function getDescription(): LocalizedText
    {
        return $this->description;
    }

    /**
     * @param LocalizedText $description
     */
    public function setDescription(LocalizedText $description): void
    {
        $this->description = $description;
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
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
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
     * @return DateTime
     */
    public function getIssued(): DateTime
    {
        return $this->issued;
    }

    /**
     * @param DateTime $issued
     */
    public function setIssued(DateTime $issued): void
    {
        $this->issued = $issued;
    }

    /**
     * @return DateTime
     */
    public function getModified(): DateTime
    {
        return $this->modified;
    }

    /**
     * @param DateTime $modified
     */
    public function setModified(DateTime $modified): void
    {
        $this->modified = $modified;
    }

    /**
     * @return Collection
     */
    public function getCatalogs(): Collection
    {
        return $this->catalogs;
    }

    /**
     * @param Collection $catalogs
     */
    public function setCatalogs(Collection $catalogs): void
    {
        $this->catalogs = $catalogs;
    }

    /**
     * @return Collection
     */
    public function getDistributions(): Collection
    {
        return $this->distributions;
    }

    /**
     * @param Collection $distributions
     */
    public function setDistributions(Collection $distributions): void
    {
        $this->distributions = $distributions;
    }

    /**
     * @return Collection
     */
    public function getAgentPoint(): Collection
    {
        return $this->contactPoint;
    }

    /**
     * @param Collection $contactPoint
     */
    public function setAgentPoint(Collection $contactPoint): void
    {
        $this->contactPoint = $contactPoint;
    }

    /**
     * @return LocalizedText
     */
    public function getKeyword(): LocalizedText
    {
        return $this->keyword;
    }

    /**
     * @param LocalizedText[] $keyword
     */
    public function setKeyword(array $keyword): void
    {
        $this->keyword = $keyword;
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

    /**
     * @return Study|null
     */
    public function getStudy(): ?Study
    {
        return $this->study;
    }

    /**
     * @param Study|null $study
     */
    public function setStudy(?Study $study): void
    {
        $this->study = $study;
    }


    public function addDistribution(Distribution $distribution)
    {
        $this->distributions[] = $distribution;
    }

    public function getAccessUrl()
    {
        return $this->catalogs->first()->getAccessUrl() . '/' . $this->slug;
    }

    public function getRelativeUrl()
    {
        return $this->catalogs->first()->getRelativeUrl() . '/' . $this->slug;
    }

    public function toBasicArray()
    {
        $publishers = [];
        foreach($this->publishers as $publisher)
        {
            /** @var Agent $publisher */
            $publishers[] = $publisher->toArray();
        }

        $contactPoints = [];
        foreach($this->contactPoint as $contactPoint)
        {
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
            'landingpage' => $this->landingPage
        ];
    }

    public function toArray()
    {
        $distributions = [];
        foreach($this->distributions as $distribution)
        {
            /** @var Distribution $distribution */
            $distributions[] = $distribution->toBasicArray();
        }

        return array_merge($this->toBasicArray(), [
            'distributions' => $distributions
        ]);
    }

    public function toGraph()
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

        foreach($this->publishers as $publisher) {
            /** @var Agent $agent */
            $publisher->addToGraph($this->getAccessUrl(), 'dcterms:publisher', $graph);
        }

        foreach($this->contactPoint as $contactPoint) {
            /** @var Agent $agent */
            $contactPoint->addToGraph($this->getAccessUrl(), 'dcat:contactPoint', $graph);
        }

        $graph->addResource($this->getAccessUrl(), 'dcterms:language', $this->language->getAccessUrl());

        $graph->addResource($this->getAccessUrl(), 'dcterms:license', $this->license->getUrl()->getValue());

        //$graph->addResource($this->getAccessUrl(), 'dcat:theme', $this->theme->getValue());

        foreach($this->distributions as $distribution) {
            $graph->addResource($this->getAccessUrl(), 'dcat:distribution', $distribution->getAccessUrl());
        }


        return $graph;
    }

    public function hasCatalog(Catalog $find)
    {
        foreach($this->catalogs as $catalog)
        {
            /** @var Catalog $catalog */
            if($catalog->getId() == $find->getId()) return true;
        }
        return false;
    }
}