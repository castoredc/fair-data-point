<?php
declare(strict_types=1);

namespace App\Entity\Terminology;

use App\Entity\Iri;

class Ontology
{
    /** @var string */
    private $name;

    /** @var Iri */
    private $url;

    /** @var string */
    private $bioPortalId;
}
