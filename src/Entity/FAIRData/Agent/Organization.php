<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Agent;

use App\Entity\FAIRData\Country;
use App\Entity\Iri;
use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_merge;
use function uniqid;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrganizationRepository")
 * @ORM\Table(name="organization", indexes={@ORM\Index(name="grid_id", columns={"grid_id"})})
 */
class Organization extends Agent
{
    public const TYPE = 'organization';

    /** @ORM\Column(type="iri", nullable=true) */
    private ?Iri $homepage = null;

    /** @ORM\Column(type="string", nullable=true) */
    private ?string $gridId = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Country",cascade={"persist"})
     * @ORM\JoinColumn(name="country", referencedColumnName="code")
     */
    private ?Country $country = null;

    /** @ORM\Column(type="string") */
    private string $city;

    /**
     * @ORM\OneToMany(targetEntity="Department", mappedBy="organization",cascade={"persist"})
     *
     * @var Collection<Department>
     */
    private Collection $departments;

    /** @ORM\Column(type="decimal", precision=10, scale=8, nullable=true) */
    private ?string $coordinatesLatitude = null;

    /** @ORM\Column(type="decimal", precision=11, scale=8, nullable=true) */
    private ?string $coordinatesLongitude = null;

    public function __construct(?string $slug, string $name, ?Iri $homepage, private ?string $countryCode = null, string $city, ?string $coordinatesLatitude, ?string $coordinatesLongitude)
    {
        $slugify = new Slugify();

        if ($slug === null) {
            $slug = $slugify->slugify($name . ' ' . uniqid());
        }

        parent::__construct($slug, $name);

        $this->homepage = $homepage;
        $this->city = $city;
        $this->coordinatesLatitude = $coordinatesLatitude;
        $this->coordinatesLongitude = $coordinatesLongitude;
    }

    public function getRelativeUrl(): string
    {
        return '/fdp/organization/' . $this->getSlug();
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

    public function getCountryCode(): ?string
    {
        return $this->countryCode;
    }

    public function setCountry(Country $country): void
    {
        $this->country = $country;
    }

    public function setCountryCode(?string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    /** @return Collection<Department> */
    public function getDepartments(): Collection
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

    public function hasCoordinates(): bool
    {
        return $this->coordinatesLatitude !== null && $this->coordinatesLongitude !== null;
    }

    public function setHomepage(?Iri $homepage): void
    {
        $this->homepage = $homepage;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /** @param ArrayCollection<Department> $departments */
    public function setDepartments(ArrayCollection $departments): void
    {
        $this->departments = $departments;
    }

    public function setCoordinatesLatitude(?string $coordinatesLatitude): void
    {
        $this->coordinatesLatitude = $coordinatesLatitude;
    }

    public function setCoordinatesLongitude(?string $coordinatesLongitude): void
    {
        $this->coordinatesLongitude = $coordinatesLongitude;
    }

    public function getGridId(): ?string
    {
        return $this->gridId;
    }

    public function setGridId(?string $gridId): void
    {
        $this->gridId = $gridId;
    }

    /** @param array<mixed> $data */
    public static function fromData(array $data): self
    {
        $organization = new Organization(
            $data['slug'] ?? null,
            $data['name'],
            $data['homepage'] ?? null,
            $data['country'] ?? null,
            $data['city'],
            isset($data['coordinatesLatitude']) && $data['coordinatesLatitude'] !== '' ? $data['coordinatesLatitude'] : null,
            isset($data['coordinatesLongitude']) && $data['coordinatesLongitude'] !== '' ? $data['coordinatesLongitude'] : null
        );

        if ($data['id'] !== null) {
            $organization->setId($data['id']);
        }

        return $organization;
    }

    /** @return array<mixed> */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'type' => self::TYPE,
            self::TYPE => [
                'id' => $this->id,
                'name' => $this->getName(),
                'country' => $this->country->getCode(),
                'city' => $this->city,
                'homepage' => $this->homepage?->getValue(),
                'coordinates' => $this->hasCoordinates() ? [
                    'lat' => $this->coordinatesLatitude,
                    'long' => $this->coordinatesLongitude,
                ] : null,
                'source' => 'database',
            ],
        ]);
    }
}
