<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF\RDFTripleElement;

use App\Entity\Data\RDF\RDFTriplePart;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("JOINED")
 * @ORM\Table(name="rdf_triple_element")
 */
abstract class RDFTripleElement implements RDFTriplePart
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    protected $id;

    public function getId(): string
    {
        return $this->id;
    }
}
