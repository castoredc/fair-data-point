<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Iri;
use App\Entity\Study;

class CreateDepartmentAndOrganizationCommand
{
    private Study $study;

    private ?string $organizationSlug = null;

    private ?string $departmentSlug = null;

    private string $name;

    private ?Iri $homepage = null;

    private string $country;

    private string $city;

    private ?string $department = null;

    private ?string $additionalInformation = null;

    private ?string $coordinatesLatitude = null;

    private ?string $coordinatesLongitude = null;

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
