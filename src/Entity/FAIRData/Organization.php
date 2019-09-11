<?php


namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;

/**
 * @ORM\Entity
 */
class Organization extends Agent
{
    /**
     * @ORM\Column(type="iri", nullable=true)
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

    public function getAccessUrl()
    {
        return $this->getFairDataPoint()->getIri() . '/agent/organization/' . $this->getSlug();
    }

    public function toArray()
    {
        return array_merge(parent::toArray(), [
            'url' => $this->homepage->getValue(),
            'homepage' => $this->homepage->getValue(),
            'type' => 'organization'
        ]);
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph)
    {
        $url = $this->getAccessUrl();
        if($this->homepage != null) $url = $this->homepage->getValue();

        $graph->addResource($url, 'a', 'foaf:Organization');
        $graph->addLiteral($url, 'foaf:name', $this->getName());

        if($subject != null && $predicate != null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }

}