<?php
declare(strict_types=1);

namespace App\Entity\DataSpecification\Common\Model;

use App\Entity\Iri;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/** @ORM\MappedSuperclass */
abstract class Predicate
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface|string $id;

    /** @ORM\Column(type="iri", nullable=false) */
    private Iri $iri;

    public function __construct(Iri $iri)
    {
        $this->iri = $iri;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getIri(): Iri
    {
        return $this->iri;
    }

    public function setIri(Iri $iri): void
    {
        $this->iri = $iri;
    }
}
