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

class Dataset
{
    /**
     * IRI of the dataset
     *
     * @var Iri
     * Required
     */
    private $iri;

    /* DC terms */

    /**
     * Name of the dataset with the language tag
     *
     * @var string
     * Required
     */
    private $title;

    /**
     * Version of the dataset
     *
     * @var string
     * Required
     */
    private $version;

    /**
     * Description of the dataset with the language tag
     *
     * @var string|null
     */
    private $description;

    /**
     * Organisation(s) or Persons(s) responsible for the dataset
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
     * The specification of the dataset metadata schema (for example ShEx)
     *
     * @var Iri|null
     */
    private $conformsTo;

    /** @var Iri|null */
    private $rights;

    /** @var Iri|null */
    private $references;

    /** @var Catalog */
    private $catalog;

    /** @var Distribution */
    private $distribution;

    /**
     * List of concepts that describe the dataset
     *
     * @var Iri
     */
    private $theme;

    /** @var Iri|null */
    private $contactPoint;

    /** @var string|null */
    private $keyword;

    /** @var Iri|null */
    private $landingPage;

    /** @var string */
    private $studyId;

    /** TODO AccessRights */

    /** TODO FDP ontology */

    /**
     * Dataset constructor.
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
     * @param Catalog $catalog
     * @param Distribution $distribution
     * @param Iri $theme
     * @param Iri|null $contactPoint
     * @param null|string $keyword
     * @param Iri|null $landingPage
     */
    public function __construct(Iri $iri, string $studyId, string $title, string $version, ?string $description, array $publishers, ?Iri $language, ?Iri $license, ?DateTime $issued, ?DateTime $modified, ?Iri $conformsTo, ?Iri $rights, ?Iri $references, Catalog $catalog, array $distribution, Iri $theme, ?Iri $contactPoint, ?string $keyword, ?Iri $landingPage)
    {
        $this->iri = $iri;
        $this->studyId = $studyId;
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
        $this->catalog = $catalog;
        $this->distribution = $distribution;
        $this->theme = $theme;
        $this->contactPoint = $contactPoint;
        $this->keyword = $keyword;
        $this->landingPage = $landingPage;
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
    public function getStudyId(): string
    {
        return $this->studyId;
    }

    /**
     * @param string $studyId
     */
    public function setStudyId(string $studyId): void
    {
        $this->studyId = $studyId;
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
     * @return Catalog
     */
    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }

    /**
     * @param Catalog $catalog
     */
    public function setCatalog(Catalog $catalog): void
    {
        $this->catalog = $catalog;
    }

    /**
     * @return Distribution
     */
    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    /**
     * @param Distribution $distribution
     */
    public function setDistribution(Distribution $distribution): void
    {
        $this->distribution = $distribution;
    }

    /**
     * @return Iri
     */
    public function getTheme(): Iri
    {
        return $this->theme;
    }

    /**
     * @param Iri $theme
     */
    public function setTheme(Iri $theme): void
    {
        $this->theme = $theme;
    }

    /**
     * @return Iri|null
     */
    public function getContactPoint(): ?Iri
    {
        return $this->contactPoint;
    }

    /**
     * @param Iri|null $contactPoint
     */
    public function setContactPoint(?Iri $contactPoint): void
    {
        $this->contactPoint = $contactPoint;
    }

    /**
     * @return null|string
     */
    public function getKeyword(): ?string
    {
        return $this->keyword;
    }

    /**
     * @param null|string $keyword
     */
    public function setKeyword(?string $keyword): void
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

    public function addDistribution(string $title, string $version, ?string $description, array $publishers, ?Iri $language, ?Iri $license, ?DateTime $issued, ?DateTime $modified, ?Iri $conformsTo, ?Iri $rights)
    {
        $this->distribution = new Distribution(
            new Iri($this->getIri() . '/distribution'),
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
            $this
        );
    }


    public function toGraph()
    {
        $graph = new EasyRdf_Graph();

        $graph->addResource($this->iri->getValue(), 'a', 'dcat:Dataset');

        $graph->addLiteral($this->iri->getValue(), 'dcterms:title', $this->title);
        $graph->addLiteral($this->iri->getValue(), 'rdfs:label', $this->title);

        $graph->addLiteral($this->iri->getValue(), 'dcterms:hasVersion', $this->version);
        $graph->addLiteral($this->iri->getValue(), 'dcterms:description', $this->description);

        foreach($this->publishers as $publisher) {
            $graph->addResource($this->iri->getValue(), 'dcterms:publisher', $publisher->getValue());
        }

        $graph->addResource($this->iri->getValue(), 'dcterms:language', $this->language->getValue());
        $graph->addResource($this->iri->getValue(), 'dcat:theme', $this->theme->getValue());

        $graph->addResource($this->iri->getValue(), 'dcat:distribution', $this->distribution->getIri()->getValue());


        return $graph;
    }
}