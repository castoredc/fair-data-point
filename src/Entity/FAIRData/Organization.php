<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;
use function array_merge;
use function time;

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
     * @ORM\ManyToOne(targetEntity="Country",cascade={"persist"})
     * @ORM\JoinColumn(name="country", referencedColumnName="code")
     *
     * @var Country|null
     */
    private $country;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $city;

    /**
     * @ORM\OneToMany(targetEntity="Department", mappedBy="organization",cascade={"persist"}, fetch="EAGER")
     *
     * @var Department[]|ArrayCollection
     */
    private $departments;

    public function __construct(?string $slug, string $name, ?Iri $homepage, Country $country, string $city)
    {
        $slugify = new Slugify();

        if ($slug === null) {
            $slug = $slugify->slugify($name . ' ' . time());
        }
        parent::__construct($slug, $name);

        $this->homepage = $homepage;
        $this->country = $country;
        $this->city = $city;
    }

    public function getAccessUrl(): string
    {
        return '/agent/organization/' . $this->getSlug();
    }

    /**
     * @return array<string>
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url' => $this->homepage->getValue(),
            'homepage' => $this->homepage->getValue(),
            'type' => 'organization',
        ]);
    }

    public function addToGraph(?string $subject, ?string $predicate, EasyRdf_Graph $graph): EasyRdf_Graph
    {
        $url = $this->getAccessUrl();
        if ($this->homepage !== null) {
            $url = $this->homepage->getValue();
        }

        $graph->addResource($url, 'a', 'foaf:Organization');
        $graph->addLiteral($url, 'foaf:name', $this->getName());

        if ($subject !== null && $predicate !== null) {
            $graph->addResource($subject, $predicate, $url);
        }

        return $graph;
    }

    public function getHomepage(): ?Iri
    {
        return $this->homepage;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return Department[]|ArrayCollection
     */
    public function getDepartments()
    {
        return $this->departments;
    }
}
