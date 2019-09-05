<?php


namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Organization extends Contact
{
    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var Iri|null
     */
    private $homepage;

    /**
     * Organization constructor.
     * @param string $slug
     * @param string $name
     * @param Iri|null $homepage
     */
    public function __construct(string $slug, string $name, ?Iri $homepage)
    {
        parent::__construct($slug, $name);
        $this->homepage = $homepage;
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'homepage' => $this->homepage
        ]);
    }

}