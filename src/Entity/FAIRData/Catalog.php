<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 20/05/2019
 * Time: 23:50
 */

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

use DateTime;
use EasyRdf_Graph;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
class Catalog
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
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="publishedCatalogs",cascade={"persist"})
     * @ORM\JoinTable(name="catalogs_publishers")
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
//     * The specification of the repository metadata schema (for example ShEx)
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
     * @ORM\ManyToOne(targetEntity="FAIRDataPoint", inversedBy="catalogs",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="fdp", referencedColumnName="id")
     *
     * @var FAIRDataPoint
     */
    private $fairDataPoint;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", inversedBy="catalogs",cascade={"persist"})
     * @ORM\JoinTable(name="catalogs_datasets")
     *
     * @var Dataset[]
     */
    private $datasets;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var Iri|null
     */
    private $homepage;

    /**
     * Catalog constructor.
     * @param string $slug
     * @param LocalizedText $title
     * @param string $version
     * @param LocalizedText $description
     * @param Contact[] $publishers
     * @param Language $language
     * @param License|null $license
     * @param DateTime $issued
     * @param DateTime $modified
     * @param Iri|null $homepage
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, array $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified, ?Iri $homepage)
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
        $this->datasets = [];
        $this->homepage = $homepage;
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
    public function getContacts(): array
    {
        return $this->publishers;
    }

    /**
     * @param Contact[] $publishers
     */
    public function setContacts(array $publishers): void
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
     * @return FAIRDataPoint
     */
    public function getFairDataPoint(): FAIRDataPoint
    {
        return $this->fairDataPoint;
    }

    /**
     * @param FAIRDataPoint $fairDataPoint
     */
    public function setFairDataPoint(FAIRDataPoint $fairDataPoint): void
    {
        $this->fairDataPoint = $fairDataPoint;
    }

    /**
     * @return Dataset[]
     */
    public function getDatasets(): array
    {
        return $this->datasets;
    }

    /**
     * @param Dataset[] $datasets
     */
    public function setDatasets(array $datasets): void
    {
        $this->datasets = $datasets;
    }

    /**
     * @return Iri|null
     */
    public function getHomepage(): ?Iri
    {
        return $this->homepage;
    }

    /**
     * @param Iri|null $homepage
     */
    public function setHomepage(?Iri $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function addDataset(Dataset $dataset)
    {
        $this->datasets[] = $dataset;
    }

    public function getAccessUrl()
    {
        return $this->fairDataPoint->getAccessUrl() . '/' . $this->slug;
    }

    public function toBasicArray()
    {
        $publishers = [];
        foreach($this->publishers as $publisher)
        {
            /** @var Contact $publisher */
            $publishers[] = $publisher->toArray();
        }

        return [
            'access_url' => $this->getAccessUrl(),
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title->toArray(),
            'version' => $this->version,
            'description' => $this->description->toArray(),
            'publishers' => $publishers,
            'language' => $this->language->toArray(),
            'license' => $this->license,
            'issued' => $this->issued,
            'modified' => $this->modified,
            'homepage' => $this->homepage
        ];
    }


    public function toArray()
    {
        $datasets = [];
        foreach($this->datasets as $dataset)
        {
            /** @var Dataset $dataset */
            $datasets[] = $dataset->toBasicArray();
        }

        return array_merge($this->toBasicArray(), [
            'datasets' => $datasets
        ]);
    }
//    /**
//     * List of taxonomy URLs
//     *
//     * @var Iri
//     */
//    private $themeTaxonomy;

    public function toGraph()
    {
        $graph = new EasyRdf_Graph();

        $graph->addResource($this->getAccessUrl(), 'a', 'dcat:Catalog');

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

//        foreach($this->publishers as $publisher) {
//            $graph->addResource($this->getAccessUrl(), 'dcterms:publisher', $publisher->getValue());
//        }

        $graph->addResource($this->getAccessUrl(), 'dcterms:language', $this->language->getAccessUrl());

        foreach($this->datasets as $dataset) {
            $graph->addResource($this->getAccessUrl(), 'dcat:dataset', $dataset->getAccessUrl());
        }

        return $graph;
    }
}