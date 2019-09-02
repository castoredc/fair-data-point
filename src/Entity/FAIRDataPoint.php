<?php
/**
 * Created by PhpStorm.
 * User: martijn
 * Date: 16/05/2019
 * Time: 14:55
 */

namespace App\Entity;


use EasyRdf_Graph;

class FAIRDataPoint
{
    /**
     * IRI of the repository
     *
     * @var Iri
     * Required
     */
    private $iri;

    /* DC terms */

    /**
     * Name of the repository with the language tag
     *
     * @var string
     * Required
     */
    private $title;

    /**
     * Version of the repository
     *
     * @var string
     * Required
     */
    private $version;

    /**
     * Description of the repository with the language tag
     *
     * @var string|null
     */
    private $description;

    /**
     * Organisation(s) responsible for the repository
     *
     * @var Iri[]
     * Required
     */
    private $publishers;

    /** @var Iri|null */
    private $language;

    /** @var Iri|null */
    private $license;

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

    /** @var Catalog[] */
    private $catalogs;

    /** TODO AccessRights */

    /** TODO FDP ontology */

    /** TODO RE3Data */

    /**
     * FAIRDataPoint constructor.
     * @param Iri $iri
     * @param string $title
     * @param string $version
     * @param null|string $description
     * @param Iri[] $publishers
     * @param Iri|null $language
     * @param Iri|null $license
     * @param Iri|null $conformsTo
     * @param Iri|null $rights
     * @param Iri|null $references
     */
    public function __construct(Iri $iri, string $title, string $version, ?string $description, array $publishers, ?Iri $language, ?Iri $license, ?Iri $conformsTo, ?Iri $rights, ?Iri $references)
    {
        $this->iri = $iri;
        $this->title = $title;
        $this->version = $version;
        $this->description = $description;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
        $this->conformsTo = $conformsTo;
        $this->rights = $rights;
        $this->references = $references;
        $this->catalogs = [];
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

    public function addCatalog(string $slug, string $title, string $version, ?string $description, array $publishers, ?Iri $language, ?Iri $license, ?DateTime $issued, ?DateTime $modified, ?Iri $conformsTo, ?Iri $rights, ?Iri $references, ?Iri $homepage, Iri $themeTaxonomy)
    {
        $this->catalogs[$slug] = new Catalog(
            new Iri($this->getIri() . '/' . $slug),
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
            $homepage,
            $themeTaxonomy
        );
    }

    public function getCatalog($slug)
    {
        return $this->catalogs[$slug];
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

        $graph->addResource($this->iri->getValue(), 'dcterms:language', $this->language->getValue());

        foreach($this->catalogs as $catalog) {
            $graph->addResource($this->iri->getValue(), 'http://www.re3data.org/schema/3-0#dataCatalog', $catalog->getIri()->getValue());
        }

        return $graph;
    }

}