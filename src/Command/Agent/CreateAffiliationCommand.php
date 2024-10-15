<?php
declare(strict_types=1);

namespace App\Command\Agent;

use App\Entity\Enum\DepartmentSource;
use App\Entity\Enum\OrganizationSource;
use App\Entity\FAIRData\Agent\Person;

class CreateAffiliationCommand
{
    public function __construct(private Person $person, private ?OrganizationSource $organizationSource, private string $organizationCountry, private ?DepartmentSource $departmentSource, private string $position, private ?string $organizationId = null, private ?string $organizationName = null, private ?string $organizationCity = null, private ?string $departmentId = null, private ?string $departmentName = null)
    {
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
