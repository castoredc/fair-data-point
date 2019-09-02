<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 20/05/2019
 * Time: 23:50
 */

namespace App\Entity;


use DateTime;
use EasyRdf_Graph;

class Catalog
{
    /**
     * IRI of the catalog
     *
     * @var Iri
     * Required
     */
    private $iri;

    /* DC terms */

    /**
     * Name of the catalog with the language tag
     *
     * @var string
     * Required
     */
    private $title;

    /**
     * Version of the catalog
     *
     * @var string
     * Required
     */
    private $version;

    /**
     * Description of the catalog with the language tag
     *
     * @var string|null
     */
    private $description;

    /**
     * Organisation(s) or Persons(s) responsible for the catalog
     *
     * @var Iri[]
     * Required
     */
    private $publishers;

    /** @var Iri|null */
    private $language;

    /** @var Iri|null */
    private $license;

    /** @var DateTime|null */
    private $issued;

    /** @var DateTime|null */
    private $modified;

    /**
     * The specification of the repository metadata schema (for example ShEx)
     *
     * @var Iri|null
     */
    private $conformsTo;

    /** @var Iri|null */
    private $rights;

    /** @var Iri|null */
    private $references;

    /** @var FAIRDataPoint */
    private $fairDataPoint;

    /** @var Dataset[] */
    private $datasets;

    /** @var Iri|null */
    private $homepage;

    /**
     * List of taxonomy URLs
     *
     * @var Iri
     */
    private $themeTaxonomy;

    /** TODO AccessRights */

    /** TODO FDP ontology */

    /** TODO RE3Data */

    /**
     * Catalog constructor.
     * @param Iri $iri
     * @param string $title
     * @param string $version
     * @param null|string $description
     * @param Iri[] $publishers
     * @param Iri|null $language
     * @param Iri|null $license
     * @param DateTime|null $issued
     * @param DateTime|null $modified
     * @param Iri|null $conformsTo
     * @param Iri|null $rights
     * @param Iri|null $references
     * @param FAIRDataPoint $fairDataPoint
     * @param Dataset[] $datasets
     * @param Iri|null $homepage
     * @param Iri $themeTaxonomy
     */
    public function __construct(Iri $iri, string $title, string $version, ?string $description, array $publishers, ?Iri $language, ?Iri $license, ?DateTime $issued, ?DateTime $modified, ?Iri $conformsTo, ?Iri $rights, ?Iri $references, FAIRDataPoint $fairDataPoint, array $datasets, ?Iri $homepage, Iri $themeTaxonomy)
    {
        $this->iri = $iri;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
        $this->issued = $issued;
        $this->modified = $modified;
        $this->conformsTo = $conformsTo;
        $this->rights = $rights;
        $this->references = $references;
        $this->fairDataPoint = $fairDataPoint;
        $this->datasets = $datasets;
        $this->homepage = $homepage;
        $this->themeTaxonomy = $themeTaxonomy;
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
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
     * @return null|string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param null|string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return Iri[]
     */
    public function getPublishers(): array
    {
        return $this->publishers;
    }

    /**
     * @param Iri[] $publishers
     */
    public function setPublishers(array $publishers): void
    {
        $this->publishers = $publishers;
    }

    /**
     * @return Iri|null
     */
    public function getLanguage(): ?Iri
    {
        return $this->language;
    }

    /**
     * @param Iri|null $language
     */
    public function setLanguage(?Iri $language): void
    {
        $this->language = $language;
    }

    /**
     * @return Iri|null
     */
    public function getLicense(): ?Iri
    {
        return $this->license;
    }

    /**
     * @param Iri|null $license
     */
    public function setLicense(?Iri $license): void
    {
        $this->license = $license;
    }

    /**
     * @return DateTime|null
     */
    public function getIssued(): ?DateTime
    {
        return $this->issued;
    }

    /**
     * @param DateTime|null $issued
     */
    public function setIssued(?DateTime $issued): void
    {
        $this->issued = $issued;
    }

    /**
     * @return DateTime|null
     */
    public function getModified(): ?DateTime
    {
        return $this->modified;
    }

    /**
     * @param DateTime|null $modified
     */
    public function setModified(?DateTime $modified): void
    {
        $this->modified = $modified;
    }

    /**
     * @return Iri|null
     */
    public function getConformsTo(): ?Iri
    {
        return $this->conformsTo;
    }

    /**
     * @param Iri|null $conformsTo
     */
    public function setConformsTo(?Iri $conformsTo): void
    {
        $this->conformsTo = $conformsTo;
    }

    /**
     * @return Iri|null
     */
    public function getRights(): ?Iri
    {
        return $this->rights;
    }

    /**
     * @param Iri|null $rights
     */
    public function setRights(?Iri $rights): void
    {
        $this->rights = $rights;
    }

    /**
     * @return Iri|null
     */
    public function getReferences(): ?Iri
    {
        return $this->references;
    }

    /**
     * @param Iri|null $references
     */
    public function setReferences(?Iri $references): void
    {
        $this->references = $references;
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
     * @return Iri
     */
    public function getHomepage(): Iri
    {
        return $this->homepage;
    }

    /**
     * @param Iri $homepage
     */
    public function setHomepage(Iri $homepage): void
    {
        $this->homepage = $homepage;
    }

    /**
     * @return Iri
     */
    public function getThemeTaxonomy(): Iri
    {
        return $this->themeTaxonomy;
    }

    /**
     * @param Iri $themeTaxonomy
     */
    public function setThemeTaxonomy(Iri $themeTaxonomy): void
    {
        $this->themeTaxonomy = $themeTaxonomy;
    }

    public function addDataset(string $slug, string $studyId, string $title, string $version, ?string $description, array $publishers, ?Iri $language, ?Iri $license, ?DateTime $issued, ?DateTime $modified, ?Iri $conformsTo, ?Iri $rights, ?Iri $references, Iri $theme, ?Iri $contactPoint, ?string $keyword, ?Iri $landingPage)
    {
        $this->datasets[$slug] = new Dataset(
            new Iri($this->getIri() . '/' . $slug),
            $studyId,
            $title,
            $version,
            $description,
            $publishers,
            $language,
            $license,
            $issued,
            $modified,
            $conformsTo,
            $rights,
            $references,
            $this,
            [],
            $theme,
            $contactPoint,
            $keyword,
            $landingPage
        );
    }

    public function getDataset($slug)
    {
        return $this->datasets[$slug];
    }

    public function toGraph()
    {
        $graph = new EasyRdf_Graph();

        $graph->addResource($this->iri->getValue(), 'a', 'dcat:Catalog');

        $graph->addLiteral($this->iri->getValue(), 'dcterms:title', $this->title);
        $graph->addLiteral($this->iri->getValue(), 'rdfs:label', $this->title);

        $graph->addLiteral($this->iri->getValue(), 'dcterms:hasVersion', $this->version);
        $graph->addLiteral($this->iri->getValue(), 'dcterms:description', $this->description);

        foreach($this->publishers as $publisher) {
            $graph->addResource($this->iri->getValue(), 'dcterms:publisher', $publisher->getValue());
        }

        $graph->addResource($this->iri->getValue(), 'dcterms:language', $this->language->getValue());

        foreach($this->datasets as $dataset) {
            $graph->addResource($this->iri->getValue(), 'dcat:dataset', $dataset->getIri()->getValue());
        }

        return $graph;
    }
}