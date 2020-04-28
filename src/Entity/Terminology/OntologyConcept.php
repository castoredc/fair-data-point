<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ontology_concept")
 */
class OntologyConcept
{
    /**
     * @ORM\Id
     * @ORM\Column(type="iri")
     *
     * @var Iri
     */
    private $url;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Ontology",cascade={"persist"})
     * @ORM\JoinColumn(name="ontology", referencedColumnName="url", nullable=false)
     *
     * @var Ontology
     */
    private $ontology;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $displayName;

    public function __construct(Iri $url, string $id, Ontology $ontology, string $displayName)
    {
        $this->url = $url;
        $this->id = $id;
        $this->ontology = $ontology;
        $this->displayName = $displayName;
    }

    public function getUrl(): Iri
    {
        return $this->url;
    }

    public function setUrl(Iri $url): void
    {
        $this->url = $url;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getOntology(): Ontology
    {
        return $this->ontology;
    }

    public function setOntology(Ontology $ontology): void
    {
        $this->ontology = $ontology;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }
}
