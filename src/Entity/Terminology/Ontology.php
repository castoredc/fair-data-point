<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'ontology')]
#[ORM\Entity]
class Ontology
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'iri')]
    private Iri $url;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private string $bioPortalId;

    public function __construct(string $name, Iri $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrl(): Iri
    {
        return $this->url;
    }

    public function setUrl(Iri $url): void
    {
        $this->url = $url;
    }

    public function getBioPortalId(): string
    {
        return $this->bioPortalId;
    }

    public function setBioPortalId(string $bioPortalId): void
    {
        $this->bioPortalId = $bioPortalId;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data): self
    {
        $ontology = new Ontology($data['name'], new Iri($data['url']));
        $ontology->setId($data['id']);

        return $ontology;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'url' => $this->url->getValue(),
            'name' => $this->name,
        ];
    }
}
