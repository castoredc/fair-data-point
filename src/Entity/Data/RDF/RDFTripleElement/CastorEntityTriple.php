<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF\RDFTripleElement;

use App\Entity\Castor\CastorEntity;
use App\Entity\Castor\Record;
use App\Entity\Data\RDF\RDFTripleObject;
use App\Entity\Data\RDF\RDFTripleSubject;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="rdf_triple_element_castor_entity")
 */
class CastorEntityTriple extends RDFTripleElement implements RDFTripleSubject, RDFTripleObject
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\CastorEntity",cascade={"persist"})
     * @ORM\JoinColumn(name="entity", referencedColumnName="id", nullable=false)
     *
     * @var CastorEntity
     */
    private $entity;

    public function __construct(CastorEntity $entity)
    {
        $this->entity = $entity;
    }

    public function getEntity(): CastorEntity
    {
        return $this->entity;
    }

    public function getLabel(): string
    {
        return $this->entity->getLabel();
    }

    public function getValue(Record $record): string
    {
        return $record->getId() . '/' . $this->entity->getSlug();
    }
}
