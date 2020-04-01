<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;
use function array_merge;

/**
 * @ORM\Entity
 * @ORM\Table(name="catalog", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
class Catalog
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
     * @var LocalizedText|null
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
     * @var LocalizedText|null
     */
    private $description;

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

    /**
     * @ORM\ManyToOne(targetEntity="FAIRDataPoint", inversedBy="catalogs",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="fdp", referencedColumnName="id")
     *
     * @var FAIRDataPoint|null
     */
    private $fairDataPoint;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", inversedBy="catalogs",cascade={"persist"})
     * @ORM\JoinTable(name="catalogs_datasets")
     *
     * @var Collection<string, Dataset>
     */
    private $datasets;

    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $homepage;

    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $logo;

    /**
     * @ORM\Column(type="boolean")
     *
     * @var bool
     */
    private $acceptSubmissions = false;

    /**
     * @param Collection<string, Agent> $publishers
     */
    public function __construct(string $slug, LocalizedText $title, string $version, LocalizedText $description, Collection $publishers, Language $language, ?License $license, DateTime $issued, DateTime $modified, ?Iri $homepage)
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
        $this->datasets = new ArrayCollection();
        $this->homepage = $homepage;
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

    public function getTitle(): LocalizedText
    {
        return $this->title;
    }

    public function setTitle(LocalizedText $title): void
    {
        $this->title = $title;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getDescription(): LocalizedText
    {
        return $this->description;
    }

    public function setDescription(LocalizedText $description): void
    {
        $this->description = $description;
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

    public function getLicense(): License
    {
        return $this->license;
    }

    public function setLicense(License $license): void
    {
        $this->license = $license;
    }

    public function getIssued(): DateTime
    {
        return $this->issued;
    }

    public function setIssued(DateTime $issued): void
    {
        $this->issued = $issued;
    }

    public function getModified(): DateTime
    {
        return $this->modified;
    }

    public function setModified(DateTime $modified): void
    {
        $this->modified = $modified;
    }

    public function getFairDataPoint(): FAIRDataPoint
    {
        return $this->fairDataPoint;
    }

    public function setFairDataPoint(FAIRDataPoint $fairDataPoint): void
    {
        $this->fairDataPoint = $fairDataPoint;
    }

    /**
     * @return Collection<string, Dataset>
     */
    public function getDatasets(bool $includeUnpublishedDatasets): Collection
    {
        if ($includeUnpublishedDatasets) {
            return $this->datasets;
        }

        $datasets = new ArrayCollection();

        foreach ($this->datasets as $dataset) {
            /** @var Dataset $dataset */
            if (! $dataset->isPublished()) {
                continue;
            }

            $datasets->set($dataset->getId(), $dataset);
        }

        return $datasets;
    }

    /**
     * @param Collection<string, Dataset> $datasets
     */
    public function setDatasets(Collection $datasets): void
    {
        $this->datasets = $datasets;
    }

    public function getHomepage(): ?Iri
    {
        return $this->homepage;
    }

    public function setHomepage(?Iri $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function addDataset(Dataset $dataset): void
    {
        $this->datasets[] = $dataset;
    }

    public function getAccessUrl(): string
    {
        return $this->fairDataPoint->getAccessUrl() . '/' . $this->slug;
    }

    public function getRelativeUrl(): string
    {
        return $this->fairDataPoint->getRelativeUrl() . '/' . $this->slug;
    }

    public function getLogo(): ?Iri
    {
        return $this->logo;
    }

    public function isAcceptingSubmissions(): bool
    {
        return $this->acceptSubmissions;
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
            'homepage' => $this->homepage !== null ? $this->homepage->getValue() : null,
            'logo' => $this->logo !== null ? $this->logo->getValue() : null,
            'acceptSubmissions' => $this->acceptSubmissions,
        ];
    }

    /**
     * @return array<mixed>
     */
    public function toArray(): array
    {
        $datasets = [];
        foreach ($this->getDatasets(false) as $dataset) {
            /** @var Dataset $dataset */
            $datasets[] = $dataset->toBasicArray();
        }

        return array_merge($this->toBasicArray(), ['datasets' => $datasets]);
    }

    public function toGraph(): EasyRdf_Graph
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

        foreach ($this->publishers as $publisher) {
            /** @var Agent $publisher */
            $publisher->addToGraph($this->getAccessUrl(), 'dcterms:publisher', $graph);
        }

        $graph->addResource($this->getAccessUrl(), 'dcterms:language', $this->language->getAccessUrl());

        foreach ($this->getDatasets(false) as $dataset) {
            $graph->addResource($this->getAccessUrl(), 'dcat:dataset', $dataset->getAccessUrl());
        }

        $graph->addResource($this->getAccessUrl(), 'dcterms:license', $this->license->getUrl()->getValue());

        return $graph;
    }
}
