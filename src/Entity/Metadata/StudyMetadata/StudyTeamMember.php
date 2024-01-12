<?php
declare(strict_types=1);

namespace App\Entity\Metadata\StudyMetadata;

use App\Entity\FAIRData\Agent\Person;
use App\Entity\Metadata\StudyMetadata;
use App\Traits\CreatedAndUpdated;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_study_team")
 * @ORM\HasLifecycleCallbacks
 */
class StudyTeamMember
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Metadata\StudyMetadata", inversedBy="studyTeamMembers", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="metadata", referencedColumnName="id", nullable=FALSE)
     */
    private StudyMetadata $metadata;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\FAIRData\Agent\Person", cascade={"persist"})
     * @ORM\JoinColumn(name="person", referencedColumnName="id", nullable=false)
     */
    private Person $person;

    /** @ORM\Column(type="boolean") */
    private bool $isContact = false;

    public function __construct(StudyMetadata $metadata, Person $person, bool $isContact)
    {
        $this->metadata = $metadata;
        $this->person = $person;
        $this->isContact = $isContact;
    }

    public function getId(): string
    {
        return $this->id;
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
