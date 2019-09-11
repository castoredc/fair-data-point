<?php


namespace App\Entity\FAIRData;

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
     * @var FAIRDataPoint
     */
    private $fairDataPoint;

    /**
     * @ORM\ManyToMany(targetEntity="Catalog", mappedBy="publishers",cascade={"persist"})
     *
     * @var Catalog[]
     */
    private $publishedCatalogs;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="publishers",cascade={"persist"})
     *
     * @var Dataset[]
     */
    private $publishedDatasets;

    /**
     * @ORM\ManyToMany(targetEntity="Distribution", mappedBy="publishers",cascade={"persist"})
     *
     * @var Distribution[]
     */
    private $publishedDistributions;

    /**
     * @ORM\ManyToMany(targetEntity="Dataset", mappedBy="contactPoint",cascade={"persist"})
     *
     * @var Dataset[]
     */
    private $contactDatasets;

    /**
     * Agent constructor.
     * @param string $slug
     * @param string $name
     */
    public function __construct(string $slug, string $name)
    {
        $this->slug = $slug;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return FAIRDataPoint
     */
    public function getFairDataPoint(): FAIRDataPoint
    {
        return $this->fairDataPoint;
    }

    public function getAccessUrl()
    {
        return $this->getFairDataPoint()->getIri() . '/agent/undefined/' . $this->slug;
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name
        ];
    }

    public function toGraph()
    {
        return $this->addToGraph(null, null, new EasyRdf_Graph());
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph)
    {
        $graph->addResource($this->getAccessUrl(), 'a', 'foaf:Agent');
        $graph->addLiteral($this->getAccessUrl(), 'foaf:name', $this->name);

        if($subject != null && $predicate != null) {
            $graph->addResource($subject, $predicate, $this->getAccessUrl());
        }

        return $graph;
    }
}