<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\Iri;
use App\Entity\Study;
use App\Entity\Terminology\CodedText;
use App\Entity\Version;
use App\Traits\CreatedAndUpdated;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_study")
 * @ORM\HasLifecycleCallbacks
 */
class StudyMetadata
{
    use CreatedAndUpdated;

    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     */
    private string $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Study", inversedBy="metadata", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id", nullable=FALSE)
     */
    private Study $study;

    /** @ORM\Column(type="version") */
    private Version $version;

    /** @ORM\Column(type="string", length=255) */
    private string $briefName;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $scientificName = null;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $briefSummary = null;

    /** @ORM\Column(type="text", nullable=true) */
    private ?string $summary = null;

    /** @ORM\Column(type="StudyType", name="study_type") */
    private StudyType $type;

    /** @ORM\Column(type="MethodType", name="method_type") */
    private MethodType $methodType;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Terminology\CodedText",cascade={"persist"})
     * @ORM\JoinColumn(name="studied_condition", referencedColumnName="id", nullable=true)
     */
    private ?CodedText $condition = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Terminology\CodedText",cascade={"persist"})
     * @ORM\JoinColumn(name="intervention", referencedColumnName="id", nullable=true)
     */
    private ?CodedText $intervention = null;

    /** @ORM\Column(type="RecruitmentStatusType", name="recruitment_status", nullable=true) */
    private ?RecruitmentStatus $recruitmentStatus = null;

    /** @ORM\Column(type="integer", nullable=true) */
    private ?int $estimatedEnrollment = null;

    /** @ORM\Column(type="boolean", nullable=true) */
    private ?bool $consentPublish = null;

    /** @ORM\Column(type="boolean", nullable=true) */
    private ?bool $consentSocialMedia = null;

    /** @ORM\Column(type="date_immutable", nullable=true) */
    private ?DateTimeImmutable $estimatedStudyStartDate = null;

    /** @ORM\Column(type="date_immutable", nullable=true) */
    private ?DateTimeImmutable $estimatedStudyCompletionDate = null;

    /** @ORM\Column(type="date_immutable", nullable=true) */
    private ?DateTimeImmutable $studyCompletionDate = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="study_contacts")
     *
     * @var Collection<Agent>
     */
    private Collection $contacts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="study_centers")
     *
     * @var Collection<Agent>
     */
    private Collection $centers;

    /** @ORM\Column(type="iri", nullable=true) */
    private ?Iri $logo = null;

    public function __construct(Study $study)
    {
        $this->study = $study;
        $this->centers = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function setStudy(Study $study): void
    {
        $this->study = $study;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getBriefName(): string
    {
        return $this->briefName;
    }

    public function setBriefName(string $briefName): void
    {
        $this->briefName = $briefName;
    }

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function setScientificName(?string $scientificName): void
    {
        $this->scientificName = $scientificName;
    }

    public function getBriefSummary(): ?string
    {
        return $this->briefSummary;
    }

    public function setBriefSummary(?string $briefSummary): void
    {
        $this->briefSummary = $briefSummary;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    public function getType(): StudyType
    {
        return $this->type;
    }

    public function setType(StudyType $type): void
    {
        $this->type = $type;
    }

    public function getCondition(): ?CodedText
    {
        return $this->condition;
    }

    public function setCondition(?CodedText $condition): void
    {
        $this->condition = $condition;
    }

    public function getIntervention(): ?CodedText
    {
        return $this->intervention;
    }

    public function setIntervention(?CodedText $intervention): void
    {
        $this->intervention = $intervention;
    }

    public function getRecruitmentStatus(): ?RecruitmentStatus
    {
        return $this->recruitmentStatus;
    }

    public function setRecruitmentStatus(?RecruitmentStatus $recruitmentStatus): void
    {
        $this->recruitmentStatus = $recruitmentStatus;
    }

    public function getEstimatedEnrollment(): ?int
    {
        return $this->estimatedEnrollment;
    }

    public function setEstimatedEnrollment(?int $estimatedEnrollment): void
    {
        $this->estimatedEnrollment = $estimatedEnrollment;
    }

    public function getEstimatedStudyStartDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyStartDate;
    }

    public function setEstimatedStudyStartDate(?DateTimeImmutable $estimatedStudyStartDate): void
    {
        $this->estimatedStudyStartDate = $estimatedStudyStartDate;
    }

    public function getEstimatedStudyCompletionDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyCompletionDate;
    }

    public function setEstimatedStudyCompletionDate(?DateTimeImmutable $estimatedStudyCompletionDate): void
    {
        $this->estimatedStudyCompletionDate = $estimatedStudyCompletionDate;
    }

    public function getStudyCompletionDate(): ?DateTimeImmutable
    {
        return $this->studyCompletionDate;
    }

    public function setStudyCompletionDate(?DateTimeImmutable $studyCompletionDate): void
    {
        $this->studyCompletionDate = $studyCompletionDate;
    }

    /**
     * @return Collection<Agent>
     */
    public function getContacts(): Collection
    {
        return $this->contacts;
    }

    /**
     * @param Collection<Agent> $contacts
     */
    public function setContacts(Collection $contacts): void
    {
        $this->contacts = $contacts;
    }

    public function addContact(Agent $contact): void
    {
        $this->contacts->add($contact);
    }

    public function removeContact(Agent $contact): void
    {
        $this->contacts->removeElement($contact);
    }

    /**
     * @return Collection<Agent>
     */
    public function getCenters(): Collection
    {
        return $this->centers;
    }

    /**
     * @return Department[]
     */
    public function getDepartments(): array
    {
        $departments = [];

        foreach ($this->centers as $center) {
            if (! $center instanceof Department) {
                continue;
            }

            $departments[] = $center;
        }

        return $departments;
    }

    /**
     * @return Organization[]
     */
    public function getOrganizations(): array
    {
        $organizations = [];

        foreach ($this->centers as $center) {
            if (! $center instanceof Organization) {
                continue;
            }

            $organizations[] = $center;
        }

        foreach ($this->getDepartments() as $department) {
            $organizations[] = $department->getOrganization();
        }

        return $organizations;
    }

    /**
     * @param Agent[]|ArrayCollection $centers
     */
    public function setCenters($centers): void
    {
        $this->centers = $centers;
    }

    public function addCenter(Agent $center): void
    {
        $this->centers[] = $center;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLogo(): ?Iri
    {
        return $this->logo;
    }

    public function setLogo(?Iri $logo): void
    {
        $this->logo = $logo;
    }

    public function hasConsentPublish(): ?bool
    {
        return $this->consentPublish;
    }

    public function setConsentPublish(bool $consentPublish): void
    {
        $this->consentPublish = $consentPublish;
    }

    public function hasConsentSocialMedia(): ?bool
    {
        return $this->consentSocialMedia;
    }

    public function setConsentSocialMedia(bool $consentSocialMedia): void
    {
        $this->consentSocialMedia = $consentSocialMedia;
    }

    public function getMethodType(): MethodType
    {
        return $this->methodType;
    }

    public function setMethodType(MethodType $methodType): void
    {
        $this->methodType = $methodType;
    }
}
