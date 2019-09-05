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
use Doctrine\ORM\Mapping as ORM;
//use EasyRdf_Graph;

/**
 * @ORM\Entity
 * @ORM\Table(name="dataset", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
class Dataset
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid")
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
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="publishedDatasets",cascade={"persist"})
     * @ORM\JoinTable(name="datasets_publishers")
     *
     * @var Contact[]
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
     * @ORM\JoinColumn(name="license", referencedColumnName="id", nullable=true)
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
     * @var Catalog[]
     */
    private $catalogs;

    /**
     * @ORM\OneToMany(targetEntity="Distribution", mappedBy="dataset",cascade={"persist"})
     * @ORM\JoinColumn(name="distribution", referencedColumnName="id")
     *
     * @var Distribution[]
     */
    private $distributions;

//    /**
//     * List of concepts that describe the dataset
//     *
//     * @var Iri
//     */
//    private $theme;

    /**
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="contactDatasets",cascade={"persist"})
     * @ORM\JoinTable(name="datasets_contactpoints")
     *
     * @var Contact[]
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
     * @param Contact[] $publishers
     * @param Language $language
     * @param License|null $license
     * @param DateTime $issued
     * @param DateTime $modified
     * @param Contact[] $contactPoint
     * @param LocalizedText $keyword
     * @param Iri|null $landingPage
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, array $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified, array $contactPoint, ?LocalizedText $keyword, ?Iri $landingPage)
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
     * @return Contact[]
     */
    public function getPublishers(): array
    {
        return $this->publishers;
    }

    /**
     * @param Contact[] $publishers
     */
    public function setPublishers(array $publishers): void
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
     * @return Catalog[]
     */
    public function getCatalogs(): array
    {
        return $this->catalogs;
    }

    /**
     * @param Catalog[] $catalogs
     */
    public function setCatalogs(array $catalogs): void
    {
        $this->catalogs = $catalogs;
    }

    /**
     * @return Distribution[]
     */
    public function getDistributions(): array
    {
        return $this->distributions;
    }

    /**
     * @param Distribution[] $distributions
     */
    public function setDistributions(array $distributions): void
    {
        $this->distributions = $distributions;
    }

    /**
     * @return Contact[]
     */
    public function getContactPoint(): array
    {
        return $this->contactPoint;
    }

    /**
     * @param Contact[] $contactPoint
     */
    public function setContactPoint(array $contactPoint): void
    {
        $this->contactPoint = $contactPoint;
    }

    /**
     * @return LocalizedText[]
     */
    public function getKeyword(): array
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

//    public function toGraph()
//    {
//        $graph = new EasyRdf_Graph();
//
//        $graph->addResource($this->iri->getValue(), 'a', 'dcat:Dataset');
//
//        $graph->addLiteral($this->iri->getValue(), 'dcterms:title', $this->title);
//        $graph->addLiteral($this->iri->getValue(), 'rdfs:label', $this->title);
//
//        $graph->addLiteral($this->iri->getValue(), 'dcterms:hasVersion', $this->version);
//        $graph->addLiteral($this->iri->getValue(), 'dcterms:description', $this->description);
//
//        foreach($this->publishers as $publisher) {
//            $graph->addResource($this->iri->getValue(), 'dcterms:publisher', $publisher->getValue());
//        }
//
//        $graph->addResource($this->iri->getValue(), 'dcterms:language', $this->language->getValue());
//        $graph->addResource($this->iri->getValue(), 'dcat:theme', $this->theme->getValue());
//
//        $graph->addResource($this->iri->getValue(), 'dcat:distribution', $this->distribution->getIri()->getValue());
//
//
//        return $graph;
//    }
}