<?php


namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Person extends Contact
{
    /**
     * @ORM\Column(type="string")
     *
     * @var Iri|null
     */
    private $orcid;

    /**
     * Organization constructor.
     * @param string $name
     * @param Iri|null $orcid
     */
    public function __construct(string $name, ?Iri $orcid)
    {
        $slugify = new Slugify();

        parent::__construct($slugify->slugify($name), $name);
        $this->orcid = $orcid;
    }



}