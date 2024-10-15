<?php
declare(strict_types=1);

namespace App\Entity\Metadata\StudyMetadata;

use App\Entity\FAIRData\Agent\Person;
use App\Entity\Metadata\StudyMetadata;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

#[ORM\Table(name: 'metadata_study_team')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class StudyTeamMember
{
    use CreatedAndUpdated;

    #[ORM\Id]
    #[ORM\Column(type: 'uuid')]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
    private UuidInterface|string $id;

    #[ORM\JoinColumn(name: 'metadata', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: StudyMetadata::class, inversedBy: 'studyTeamMembers', cascade: ['persist', 'remove'])]
    private StudyMetadata $metadata;

    #[ORM\JoinColumn(name: 'person', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Person::class, cascade: ['persist'])]
    private Person $person;

    #[ORM\Column(type: 'boolean')]
    private bool $isContact = false;

    public function __construct(StudyMetadata $metadata, Person $person, bool $isContact)
    {
        $this->metadata = $metadata;
        $this->person = $person;
        $this->isContact = $isContact;
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getMetadata(): StudyMetadata
    {
        return $this->metadata;
    }

    public function getPerson(): Person
    {
        return $this->person;
    }

    public function isContact(): bool
    {
        return $this->isContact;
    }
}
