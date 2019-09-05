<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 20/05/2019
 * Time: 23:50
 */

namespace App\Entity\FAIRData;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="distribution", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
class Distribution
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
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="publishedDistributions",cascade={"persist"})
     * @ORM\JoinTable(name="distributions_publishers")
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

    /**
     * @ORM\ManyToOne(targetEntity="Dataset", inversedBy="distributions",cascade={"persist"})
     *
     * @var Distribution
     */
    private $dataset;

    /**
     * Distribution constructor.
     * @param string $slug
     * @param LocalizedText $title
     * @param string $version
     * @param LocalizedText $description
     * @param Contact[] $publishers
     * @param Language $language
     * @param License $license
     * @param DateTime $issued
     * @param DateTime $modified
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, array $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified)
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
     * @return License
     */
    public function getLicense(): License
    {
        return $this->license;
    }

    /**
     * @param License $license
     */
    public function setLicense(License $license): void
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
     * @return Distribution
     */
    public function getDataset(): Distribution
    {
        return $this->dataset;
    }

    /**
     * @param Distribution $dataset
     */
    public function setDataset(Distribution $dataset): void
    {
        $this->dataset = $dataset;
    }

//    /** @var string|null */
//    private $format;
//
//    /** @var double */
//    private $byteSize;




    /** TODO AccessRights */

    /** TODO FDP ontology */


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
//
//        $graph->addResource($this->iri->getValue(), 'dcat:downloadURL', $this->iri->getValue() . '/rdf?download=1');
//        $graph->addResource($this->iri->getValue(), 'dcat:accessURL', $this->iri->getValue() . '/rdf');
//        $graph->addLiteral($this->iri->getValue(), 'dcat:mediaType', 'text/turtle');
//
//        return $graph;
//    }
}