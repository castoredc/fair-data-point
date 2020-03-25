<?php

namespace App\Message\Api\Study;

use App\Entity\FAIRData\Country;
use App\Entity\Iri;

class CreateDepartmentAndOrganizationCommand
{
    /** @var string
     */
    private $studyId;

    /** @var string|null
     */
    private $organizationSlug;

    /** @var string|null
     */
    private $departmentSlug;

    /** @var string
     */
    private $name;

    /** @var Iri|null
     */
    private $homepage;

    /** @var string
     */
    private $country;

    /** @var string
     */
    private $city;

    /** @var string|null
     */
    private $department;

    /** @var string|null
     */
    private $additionalInformation;

    /**
     * CreateDepartmentAndOrganizationCommand constructor.
     *
     * @param string $studyId
     * @param string|null $organizationSlug
     * @param string|null $departmentSlug
     * @param string $name
     * @param Iri|null $homepage
     * @param string $country
     * @param string $city
     * @param string|null $department
     * @param string|null $additionalInformation
     */
    public function __construct(
        string $studyId,
        ?string $organizationSlug,
        ?string $departmentSlug,
        string $name,
        ?Iri $homepage,
        string $country,
        string $city,
        ?string $department,
        ?string $additionalInformation
    ) {
        $this->studyId = $studyId;
        $this->organizationSlug = $organizationSlug;
        $this->departmentSlug = $departmentSlug;
        $this->name = $name;
        $this->homepage = $homepage;
        $this->country = $country;
        $this->city = $city;
        $this->department = $department;
        $this->additionalInformation = $additionalInformation;
    }

    /**
     * @return string
     */
    public function getStudyId(): string
    {
        return $this->studyId;
    }

    /**
     * @return string|null
     */
    public function getOrganizationSlug(): ?string
    {
        return $this->organizationSlug;
    }

    /**
     * @return string|null
     */
    public function getDepartmentSlug(): ?string
    {
        return $this->departmentSlug;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Iri|null
     */
    public function getHomepage(): ?Iri
    {
        return $this->homepage;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string|null
     */
    public function getDepartment(): ?string
    {
        return $this->department;
    }

    /**
     * @return string|null
     */
    public function getAdditionalInformation(): ?string
    {
        return $this->additionalInformation;
    }
}