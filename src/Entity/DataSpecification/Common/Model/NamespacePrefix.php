<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Model;

use App\Entity\Iri;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\MappedSuperclass]
abstract class NamespacePrefix
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\Column(type: 'string')]
    private string $prefix;

    #[ORM\Column(type: 'iri', nullable: false)]
    private Iri $uri;

    public function __construct(string $prefix, Iri $uri)
    {
        $this->prefix = $prefix;
        $this->uri = $uri;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getPrefix(): string
    {
        return $this->prefix;
    }

    public function getUri(): Iri
    {
        return $this->uri;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setUri(Iri $uri): void
    {
        $this->uri = $uri;
    }
}
