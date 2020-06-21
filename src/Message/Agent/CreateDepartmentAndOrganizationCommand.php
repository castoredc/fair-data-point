<?php
declare(strict_types=1);

namespace App\Message\Agent;

use App\Entity\Iri;
use App\Entity\Study;

class CreateDepartmentAndOrganizationCommand
{
    /** @var Study */
    private $study;

    /** @var string|null */
    private $organizationSlug;

    /** @var string|null */
    private $departmentSlug;

    /** @var string */
    private $name;

    /** @var Iri|null */
    private $homepage;

    /** @var string */
    private $country;

    /** @var string */
    private $city;

    /** @var string|null */
    private $department;

    /** @var string|null */
    private $additionalInformation;

    /** @var string|null */
    private $coordinatesLatitude;

    /** @var string|null */
    private $coordinatesLongitude;

    public function __construct(
        Study $study,
        ?string $organizationSlug,
        ?string $departmentSlug,
        string $name,
        ?Iri $homepage,
        string $country,
        string $city,
        ?string $department,
        ?string $additionalInformation,
        ?string $coordinatesLatitude,
        ?string $coordinatesLongitude
    ) {
        $this->study = $study;
        $this->organizationSlug = $organizationSlug;
        $this->departmentSlug = $departmentSlug;
        $this->name = $name;
        $this->homepage = $homepage;
        $this->country = $country;
        $this->city = $city;
        $this->department = $department;
        $this->additionalInformation = $additionalInformation;
        $this->coordinatesLatitude = $coordinatesLatitude;
        $this->coordinatesLongitude = $coordinatesLongitude;
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function getOrganizationSlug(): ?string
    {
        return $this->organizationSlug;
    }

    public function getDepartmentSlug(): ?string
    {
        return $this->departmentSlug;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getHomepage(): ?Iri
    {
        return $this->homepage;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
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
