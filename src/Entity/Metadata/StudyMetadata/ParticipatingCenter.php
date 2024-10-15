<?php
declare(strict_types=1);

namespace App\Entity\Metadata\StudyMetadata;

use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\Metadata\StudyMetadata;
use App\Traits\CreatedAndUpdated;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'metadata_study_centers')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class ParticipatingCenter
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'metadata', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\Metadata\StudyMetadata::class, inversedBy: 'participatingCenters', cascade: ['persist', 'remove'])]
    private StudyMetadata $metadata;

    #[ORM\JoinColumn(name: 'organization', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: \App\Entity\FAIRData\Agent\Organization::class, cascade: ['persist'])]
    private Organization $organization;

    /**
     *
     * @var Collection<Department>
     */
    #[ORM\JoinTable(name: 'metadata_study_centers_departments')]
    #[ORM\ManyToMany(targetEntity: \App\Entity\FAIRData\Agent\Department::class, cascade: ['persist'])]
    private Collection $departments;

    public function __construct(StudyMetadata $metadata, Organization $organization)
    {
        $this->metadata = $metadata;
        $this->organization = $organization;
        $this->departments = new ArrayCollection();
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getMetadata(): StudyMetadata
    {
        return $this->metadata;
    }

    public function getOrganization(): Organization
    {
        return $this->organization;
    }

    /** @return Collection<Department> */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }
}
