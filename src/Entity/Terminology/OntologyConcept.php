<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;

class OntologyConcept
{
    /** @var Iri */
    private $url;

    /** @var string */
    private $id;

    /** @var Ontology */
    private $ontology;

    /** @var string */
    private $displayName;
}
