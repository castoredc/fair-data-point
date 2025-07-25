<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;
use App\Repository\OntologyConceptRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'ontology_concept')]
#[ORM\Index(name: 'ontology_code', columns: ['ontology', 'code'])]
#[ORM\Entity(repositoryClass: OntologyConceptRepository::class)]
class OntologyConcept
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'iri')]
    private Iri $url;

    #[ORM\Column(type: 'string')]
    private string $code;

    #[ORM\JoinColumn(name: 'ontology', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Ontology::class, cascade: ['persist'])]
    private Ontology $ontology;

    #[ORM\Column(type: 'string')]
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
        return (string) $this->id;
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

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'url' => $this->url->getValue(),
            'code' => $this->code,
            'ontology' => $this->ontology->toArray(),
            'displayName' => $this->displayName,
        ];
    }

    /** @param array<mixed> $data */
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
