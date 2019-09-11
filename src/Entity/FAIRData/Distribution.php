<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 20/05/2019
 * Time: 23:50
 */

namespace App\Entity\FAIRData;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
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
     * @ORM\ManyToMany(targetEntity="Agent", inversedBy="publishedDistributions",cascade={"persist"})
     * @ORM\JoinTable(name="distributions_publishers")
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
     * @param Collection $publishers
     * @param Language $language
     * @param License $license
     * @param DateTime $issued
     * @param DateTime $modified
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified)
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
     * @return Agent[]
     */
    public function getPublishers(): array
    {
        return $this->publishers;
    }

    /**
     * @param Agent[] $publishers
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

    public function getAccessUrl()
    {
        return $this->dataset->getAccessUrl() . '/' . $this->slug;
    }

    public function getRelativeUrl()
    {
        return $this->dataset->getRelativeUrl() . '/' . $this->slug;
    }

    public function toBasicArray()
    {
        $publishers = [];
        foreach($this->publishers as $publisher)
        {
            /** @var Agent $publisher */
            $publishers[] = $publisher->toArray();
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
        ];
    }

    public function toArray()
    {
        return $this->toBasicArray();
    }

//    /** @var string|null */
//    private $format;
//
//    /** @var double */
//    private $byteSize;




    /** TODO AccessRights */

    /** TODO FDP ontology */


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

        $graph->addResource($this->getAccessUrl(), 'dcterms:language', $this->language->getAccessUrl());

        $graph->addResource($this->getAccessUrl(), 'dcterms:license', $this->license->getUrl()->getValue());

        return $graph;
    }
}