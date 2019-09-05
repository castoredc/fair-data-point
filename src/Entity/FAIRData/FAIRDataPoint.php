<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 16/05/2019
 * Time: 14:55
 */

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;

/**
 * @ORM\Entity
 * @ORM\Table(name="fdp", indexes={@ORM\Index(name="iri", columns={"iri"})})
 */
class FAIRDataPoint
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
     * @var Iri
     */
    private $iri;

    /* DC terms */

    /**
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"}, fetch = "EAGER")
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
     * @ORM\OneToOne(targetEntity="LocalizedText",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="description", referencedColumnName="id")
     *
     * @var LocalizedText
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Contact", inversedBy="publishedCatalogs",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinTable(name="fdp_publishers")
     *
     * @var Collection
     */
    private $publishers;

    /**
     * @ORM\ManyToOne(targetEntity="Language",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="language", referencedColumnName="code")
     *
     * @var Language
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="License",cascade={"persist"}, fetch = "EAGER")
     * @ORM\JoinColumn(name="license", referencedColumnName="id", nullable=true))
     *
     * @var License|null
     */
    private $license;

    /**
     * @ORM\OneToMany(targetEntity="Catalog", mappedBy="fairDataPoint",cascade={"persist"}, fetch = "EAGER")
     * @var Collection
     */
    private $catalogs;

    /**
     * FAIRDataPoint constructor.
     * @param Iri $iri
     * @param LocalizedText $title
     * @param string $version
     * @param LocalizedText $description
     * @param ArrayCollection $publishers
     * @param Language $language
     * @param License $license
     */
    public function __construct(Iri $iri, LocalizedText $title, string $version, LocalizedText $description, ArrayCollection $publishers, Language $language, ?License $license)
    {
        $this->iri = $iri;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
        $this->catalogs = [];
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
     * @return Iri
     */
    public function getIri(): Iri
    {
        return $this->iri;
    }

    /**
     * @param Iri $iri
     */
    public function setIri(Iri $iri): void
    {
        $this->iri = $iri;
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

    public function addCatalog(Catalog $catalog)
    {
        $this->catalogs->add($catalog);
    }

    public function toJson()
    {
        return [
            'iri' => $this->iri,
            'title' => $this->title->toArray(),
            'version' => $this->version,
            'description' => $this->description,
            'publishers' => $this->publishers,
            'language' => $this->language,
            'license' => $this->license,
            'catalogs' => $this->catalogs
        ];
    }

    public function toGraph()
    {
        $graph = new EasyRdf_Graph();

        $graph->addResource($this->iri->getValue(), 'a', 'r3d:Repository');

        $graph->addLiteral($this->iri->getValue(), 'dcterms:title', $this->title);
        $graph->addLiteral($this->iri->getValue(), 'rdfs:label', $this->title);

        $graph->addLiteral($this->iri->getValue(), 'dcterms:hasVersion', $this->version);
        $graph->addLiteral($this->iri->getValue(), 'dcterms:description', $this->description);

        foreach($this->publishers as $publisher) {
            $graph->addResource($this->iri->getValue(), 'dcterms:publisher', $publisher->getValue());
        }

        $graph->addResource($this->iri->getValue(), 'dcterms:language', $this->language->getCode());

        foreach($this->catalogs as $catalog) {
            $graph->addResource($this->iri->getValue(), 'http://www.re3data.org/schema/3-0#dataCatalog', $catalog->getIri()->getValue());
        }

        return $graph;
    }

}