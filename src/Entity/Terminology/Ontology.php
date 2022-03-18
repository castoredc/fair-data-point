<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="ontology")
 */
class Ontology
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
    private string $name;

    /** @ORM\Column(type="string") */
    private string $bioPortalId;

    public function __construct(string $name, Iri $url)
    {
        $this->name = $name;
        $this->url = $url;
    }

    public function getId(): string
    {
        return $this->id;
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
}
