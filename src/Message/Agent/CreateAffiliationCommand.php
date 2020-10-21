<?php
declare(strict_types=1);

namespace App\Message\Agent;

use App\Entity\Enum\DepartmentSource;
use App\Entity\Enum\OrganizationSource;
use App\Entity\FAIRData\Agent\Person;

class CreateAffiliationCommand
{
    private Person $person;
    private ?OrganizationSource $organizationSource;
    private ?string $organizationId = null;
    private ?string $organizationName = null;
    private ?string $organizationCity = null;
    private string $organizationCountry;
    private ?DepartmentSource $departmentSource;
    private ?string $departmentId = null;
    private ?string $departmentName = null;
    private string $position;

    public function __construct(Person $person, ?OrganizationSource $organizationSource, ?string $organizationId, ?string $organizationName, ?string $organizationCity, string $organizationCountry, ?DepartmentSource $departmentSource, ?string $departmentId, ?string $departmentName, string $position)
    {
        $this->person = $person;
        $this->organizationSource = $organizationSource;
        $this->organizationId = $organizationId;
        $this->organizationName = $organizationName;
        $this->organizationCity = $organizationCity;
        $this->organizationCountry = $organizationCountry;
        $this->departmentSource = $departmentSource;
        $this->departmentId = $departmentId;
        $this->departmentName = $departmentName;
        $this->position = $position;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function getOrganizationId(): ?string
    {
        return $this->organizationId;
    }

    public function getOrganizationSource(): ?OrganizationSource
    {
        return $this->organizationSource;
    }

    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    public function getOrganizationCity(): ?string
    {
        return $this->organizationCity;
    }

    public function getOrganizationCountry(): string
    {
        return $this->organizationCountry;
    }

    public function getDepartmentSource(): ?DepartmentSource
    {
        return $this->departmentSource;
    }

    public function getDepartmentId(): ?string
    {
        return $this->departmentId;
    }

    public function getDepartmentName(): ?string
    {
        return $this->departmentName;
    }

    public function getPosition(): string
    {
        return $this->position;
    }
}
