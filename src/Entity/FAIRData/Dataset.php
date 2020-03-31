<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Castor\Study;
use App\Entity\FAIRData\Distribution\Distribution;
use App\Entity\Iri;
use Doctrine\Common\Collections\ArrayCollection;
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

    /** @var Collection<string, Agent> */
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
     * @ORM\OneToOne(targetEntity="App\Entity\Castor\Study",cascade={"persist"}, inversedBy="dataset")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=true)
     *
     * @var Study|null
     */
    private $study;

    /**
     * @param Collection<string, Agent> $publishers
     */
    public function __construct(string $slug, Collection $publishers, Language $language, ?License $license, ?LocalizedText $keyword, ?Iri $landingPage)
    {
        $this->slug = $slug;
        $this->publishers = $publishers;
        $this->language = $language;
        $this->license = $license;
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

    /**
     * @return array<mixed>
     */
    public function toBasicArray(): array
    {
        $publishers = [];
        // foreach ($this->publishers as $publisher) {
        //     /** @var Agent $publisher */
        //     $publishers[] = $publisher->toArray();
        // }

        $metadata = $this->study->getLatestMetadata();

        $contactPoints = [];
        foreach ($metadata->getContacts() as $contactPoint) {
            /** @var Agent $contactPoint */
            $contactPoints[] = $contactPoint->toArray();
        }

        $organizations = [];
        foreach ($metadata->getCenters() as $organization) {
            /** @var Organization $center */
            $organizations[] = $organization->toArray();
        }

        $title = new LocalizedText(new ArrayCollection([new LocalizedTextItem($metadata->getBriefName(), $this->language)]));

        $shortDescription = null;

        if ($metadata->getBriefSummary() !== null) {
            $shortDescription = (new LocalizedText(new ArrayCollection([new LocalizedTextItem($metadata->getBriefSummary(), $this->language)])))->toArray();
        }

        $description = null;

        if ($metadata->getSummary() !== null) {
            $description = (new LocalizedText(new ArrayCollection([new LocalizedTextItem($metadata->getSummary(), $this->language)])))->toArray();
        }

        return [
            'access_url' => $this->getAccessUrl(),
            'relative_url' => $this->getRelativeUrl(),
            'id' => $this->id,
            'studyId' => $this->study->getId(),
            'slug' => $this->slug,
            'title' => $title->toArray(),
            'version' => $this->study->getLatestMetadataVersion(),
            'shortDescription' => $shortDescription,
            'description' => $description,
            'publishers' => $publishers,
            'language' => $this->language->toArray(),
            'license' => $this->license !== null ? $this->license->toArray() : null,
            'issued' => $metadata->getCreated(),
            'modified' => $metadata->getUpdated(),
            'contactPoints' => $contactPoints,
            'organizations' => $organizations,
//            'keyword' => $this->keyword->toArray(),
            'landingpage' => $this->landingPage !== null ? $this->landingPage->getValue() : null,
            'logo' => $metadata->getLogo() !== null ? $metadata->getLogo()->getValue() : null,
            'recruitmentStatus' => $metadata->getRecruitmentStatus() !== null ? $metadata->getRecruitmentStatus()->toString() : null,
            'estimatedEnrollment' => $metadata->getEstimatedEnrollment(),
            'studyType' => $metadata->getType()->toString(),
            'condition' => $metadata->getCondition() !== null ? $metadata->getCondition()->toArray() : null,
            'intervention' => $metadata->getIntervention() !== null ? $metadata->getIntervention()->toArray() : null,
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

        // foreach ($this->title->getTexts() as $text) {
        //     /** @var LocalizedTextItem $text */
        //     $graph->addLiteral($this->getAccessUrl(), 'dcterms:title', $text->getText(), $text->getLanguage()->getCode());
        //     $graph->addLiteral($this->getAccessUrl(), 'rdfs:label', $text->getText(), $text->getLanguage()->getCode());
        // }

        // $graph->addLiteral($this->getAccessUrl(), 'dcterms:hasVersion', $this->version);

        // foreach ($this->description->getTexts() as $text) {
        //     /** @var LocalizedTextItem $text */
        //     $graph->addLiteral($this->getAccessUrl(), 'dcterms:description', $text->getText(), $text->getLanguage()->getCode());
        // }

        foreach ($this->publishers as $publisher) {
            /** @var Agent $publisher */
            $publisher->addToGraph($this->getAccessUrl(), 'dcterms:publisher', $graph);
        }

        // foreach ($this->contactPoint as $contactPoint) {
        //     /** @var Agent $contactPoint */
        //     $contactPoint->addToGraph($this->getAccessUrl(), 'dcat:contactPoint', $graph);
        // }

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
