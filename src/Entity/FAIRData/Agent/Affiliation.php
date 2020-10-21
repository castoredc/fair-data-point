<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Agent;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Affiliation
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="Person", cascade={"persist"}, inversedBy="affiliations")
     * @ORM\JoinColumn(name="person", referencedColumnName="id", nullable=false)
     */
    private Person $person;

    /**
     * @ORM\ManyToOne(targetEntity="Organization",cascade={"persist"})
     * @ORM\JoinColumn(name="organization", referencedColumnName="id", nullable=false)
     */
    private Organization $organization;

    /**
     * @ORM\ManyToOne(targetEntity="Department",cascade={"persist"})
     * @ORM\JoinColumn(name="department", referencedColumnName="id", nullable=false)
     */
    private Department $department;

    /** @ORM\Column(type="string") */
    private string $position;

    public function __construct(Person $person, Organization $organization, Department $department, string $position)
    {
        $this->person = $person;
        $this->organization = $organization;
        $this->department = $department;
        $this->position = $position;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function setPerson(Person $person): void
    {
        $this->person = $person;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    public function setOrganization(Organization $organization): void
    {
        $this->organization = $organization;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): void
    {
        $this->department = $department;
    }

    public function getPosition(): string
    {
        return $this->position;
    }

    public function setPosition(string $position): void
    {
        $this->position = $position;
    }
}
