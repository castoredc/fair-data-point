<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Castor\CastorEntity;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'annotation')]
#[ORM\Entity]
class Annotation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'entity', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: CastorEntity::class, inversedBy: 'annotations', cascade: ['persist'])]
    private CastorEntity $entity;

    #[ORM\JoinColumn(name: 'concept', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: OntologyConcept::class, cascade: ['persist'])]
    private OntologyConcept $concept;

    public function __construct(CastorEntity $entity, OntologyConcept $concept)
    {
        $this->entity = $entity;
        $this->concept = $concept;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getEntity(): CastorEntity
    {
        return $this->entity;
    }

    public function setEntity(CastorEntity $entity): void
    {
        $this->entity = $entity;
    }

    public function getConcept(): OntologyConcept
    {
        return $this->concept;
    }

    public function setConcept(OntologyConcept $concept): void
    {
        $this->concept = $concept;
    }
}
