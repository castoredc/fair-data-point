<?php
declare(strict_types=1);

namespace App\Entity\Data\RDF\RDFTripleElement;

use App\Entity\Castor\Record;
use App\Entity\Data\RDF\RDFTripleObject;
use App\Entity\Data\RDF\RDFTripleSubject;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 */
class RecordTriple extends RDFTripleElement implements RDFTripleSubject, RDFTripleObject
{
    public function getLabel(): string
    {
        return 'Castor record';
    }

    public function getValue(Record $record): string
    {
        return $record->getId();
    }
}
