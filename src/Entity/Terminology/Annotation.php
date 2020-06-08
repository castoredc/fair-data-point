<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Castor\CastorEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="annotation")
 */
class Annotation
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
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\CastorEntity", inversedBy="annotations", cascade={"persist"})
     * @ORM\JoinColumn(name="entity", referencedColumnName="id", nullable=false)
     *
     * @var CastorEntity
     */
    private $entity;

    /**
     * @ORM\ManyToOne(targetEntity="OntologyConcept",cascade={"persist"})
     * @ORM\JoinColumn(name="concept", referencedColumnName="id", nullable=false)
     *
     * @var OntologyConcept
     */
    private $concept;

    public function __construct(CastorEntity $entity, OntologyConcept $concept)
    {
        $this->entity = $entity;
        $this->concept = $concept;
    }

    public function getId(): string
    {
        return $this->id;
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
