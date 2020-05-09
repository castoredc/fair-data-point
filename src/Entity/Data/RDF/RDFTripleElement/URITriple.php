<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF\RDFTripleElement;

use App\Entity\Castor\Record;
use App\Entity\Data\RDF\RDFTripleObject;
use App\Entity\Data\RDF\RDFTriplePredicate;
use App\Entity\Data\RDF\RDFTripleSubject;
use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="rdf_triple_element_uri")
 */
class URITriple extends RDFTripleElement implements RDFTripleSubject, RDFTriplePredicate, RDFTripleObject
{
    /**
     * @ORM\Column(type="iri", nullable=false)
     *
     * @var Iri
     */
    private $uri;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $label;

    public function __construct(Iri $uri, string $label)
    {
        $this->uri = $uri;
        $this->label = $label;
    }

    public function getValue(Record $record): string
    {
        return $this->uri->getValue();
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
