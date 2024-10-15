<?php
declare(strict_types=1);

namespace App\Entity\FAIRData\Agent;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity]
class Affiliation
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'person', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Person::class, cascade: ['persist'], inversedBy: 'affiliations')]
    private Person $person;

    #[ORM\JoinColumn(name: 'organization', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Organization::class, cascade: ['persist'])]
    private Organization $organization;

    #[ORM\JoinColumn(name: 'department', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \Department::class, cascade: ['persist'])]
    private Department $department;

    #[ORM\Column(type: 'string')]
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
        return (string) $this->id;
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
