<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="agent", indexes={@ORM\Index(name="slug", columns={"slug"})})
 */
abstract class Agent
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

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="FAIRDataPoint", inversedBy="catalogs",cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="fdp", referencedColumnName="id")
     *
     * @var FAIRDataPoint|null
     */
    private $fairDataPoint;

    /**
     * @ORM\ManyToMany(targetEntity="Catalog", mappedBy="publishers",cascade={"persist"})
     *
     * @var Collection<string, Catalog>
     */
    private $publishedCatalogs;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="publishers",cascade={"persist"})
     *
     * @var Collection<string, Dataset>
     */
    private $publishedDatasets;

    /**
     * @ORM\ManyToMany(targetEntity="Distribution", mappedBy="publishers",cascade={"persist"})
     *
     * @var Collection<string, Distribution>
     */
    private $publishedDistributions;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="contactPoint",cascade={"persist"})
     *
     * @var Collection<string, Dataset>
     */
    private $contactDatasets;

    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getFairDataPoint(): FAIRDataPoint
    {
        return $this->fairDataPoint;
    }

    public function getAccessUrl(): string
    {
        return $this->getFairDataPoint()->getIri() . '/agent/undefined/' . $this->slug;
    }

    /**
     * @return Collection<string, Catalog>
     */
    public function getPublishedCatalogs(): Collection
    {
        return $this->publishedCatalogs;
    }

    /**
     * @param Collection<string, Catalog> $publishedCatalogs
     */
    public function setPublishedCatalogs(Collection $publishedCatalogs): void
    {
        $this->publishedCatalogs = $publishedCatalogs;
    }

    /**
     * @return Collection<string, Dataset>
     */
    public function getPublishedDatasets(): Collection
    {
        return $this->publishedDatasets;
    }

    /**
     * @param Collection<string, Dataset> $publishedDatasets
     */
    public function setPublishedDatasets(Collection $publishedDatasets): void
    {
        $this->publishedDatasets = $publishedDatasets;
    }

    /**
     * @return Collection<string, Distribution>
     */
    public function getPublishedDistributions(): Collection
    {
        return $this->publishedDistributions;
    }

    /**
     * @param Collection<string, Distribution> $publishedDistributions
     */
    public function setPublishedDistributions(Collection $publishedDistributions): void
    {
        $this->publishedDistributions = $publishedDistributions;
    }

    /**
     * @return Collection<string, Dataset>
     */
    public function getContactDatasets(): Collection
    {
        return $this->contactDatasets;
    }

    /**
     * @param Collection<string, Dataset> $contactDatasets
     */
    public function setContactDatasets(Collection $contactDatasets): void
    {
        $this->contactDatasets = $contactDatasets;
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
        ];
    }

    public function toGraph(): EasyRdf_Graph
    {
        return $this->addToGraph(null, null, new EasyRdf_Graph());
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        $graph->addResource($this->getAccessUrl(), 'a', 'foaf:Agent');
        $graph->addLiteral($this->getAccessUrl(), 'foaf:name', $this->name);

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $this->getAccessUrl());
        }

        return $graph;
    }
}
