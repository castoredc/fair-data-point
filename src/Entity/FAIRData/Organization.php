<?php
declare(strict_types=1);

namespace App\Entity\FAIRData;

use App\Entity\Iri;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use EasyRdf_Graph;
use function array_merge;
use function uniqid;

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

    /**
     * @ORM\Column(type="decimal", precision=10, scale=8, nullable=true)
     *
     * @var string|null
     */
    private $coordinatesLatitude;

    /**
     * @ORM\Column(type="decimal", precision=11, scale=8, nullable=true)
     *
     * @var string|null
     */
    private $coordinatesLongitude;

    public function __construct(?string $slug, string $name, ?Iri $homepage, Country $country, string $city)
    {
        $slugify = new Slugify();

        if ($slug === null) {
            $slug = $slugify->slugify($name . ' ' . uniqid());
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
        $coordinates = null;

        if ($this->coordinatesLatitude !== null && $this->coordinatesLongitude !== null) {
            $coordinates = [];
            $coordinates['lat'] = $this->coordinatesLatitude;
            $coordinates['long'] = $this->coordinatesLongitude;
        }

        return array_merge(parent::toArray(), [
            'url' => $this->homepage !== null ? $this->homepage->getValue() : null,
            'homepage' => $this->homepage !== null ? $this->homepage->getValue() : null,
            'type' => 'organization',
            'city' => $this->city,
            'country' => $this->country->getName(),
            'coordinates' => $coordinates,
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

    public function getCoordinatesLatitude(): ?string
    {
        return $this->coordinatesLatitude;
    }

    public function getCoordinatesLongitude(): ?string
    {
        return $this->coordinatesLongitude;
    }
}
