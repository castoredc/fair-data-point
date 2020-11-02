<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OntologyConceptRepository")
 * @ORM\Table(name="ontology_concept", indexes={@ORM\Index(name="ontology_code", columns={"ontology","code"})})
 */
class OntologyConcept
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /** @ORM\Column(type="iri") */
    private Iri $url;

    /** @ORM\Column(type="string") */
    private string $code;

    /**
     * @ORM\ManyToOne(targetEntity="Ontology",cascade={"persist"})
     * @ORM\JoinColumn(name="ontology", referencedColumnName="id", nullable=false)
     */
    private Ontology $ontology;

    /** @ORM\Column(type="string") */
    private string $displayName;

    public function __construct(Iri $url, string $code, Ontology $ontology, string $displayName)
    {
        $this->url = $url;
        $this->code = $code;
        $this->ontology = $ontology;
        $this->displayName = $displayName;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUrl(): Iri
    {
        return $this->url;
    }

    public function setUrl(Iri $url): void
    {
        $this->url = $url;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
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

    /**
     * @param array<mixed> $data
     */
    public static function fromData(array $data): self
    {
        return new OntologyConcept(
            new Iri($data['url']),
            $data['code'],
            Ontology::fromData($data['ontology']),
            $data['displayName'],
        );
    }
}
