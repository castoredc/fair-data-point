<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Castor\Study;
use App\Entity\Enum\MethodType;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Agent;
use App\Entity\FAIRData\Department;
use App\Entity\FAIRData\Organization;
use App\Entity\Iri;
use App\Entity\Terminology\CodedText;
use DateTime;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="metadata_study")
 * @ORM\HasLifecycleCallbacks
 */
class StudyMetadata
{
    /**
     * @ORM\Id
     * @ORM\Column(type="guid", length=190)
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Castor\Study", inversedBy="metadata", cascade={"persist"}, fetch="EAGER")
     * @ORM\JoinColumn(name="study_id", referencedColumnName="id")
     *
     * @var Study|null
     */
    private $study;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $briefName;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $scientificName;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $briefSummary;

    /**
     * @ORM\Column(type="text", nullable=true)
     *
     * @var string|null
     */
    private $summary;

    /**
     * @ORM\Column(type="StudyType", name="study_type")
     *
     * @var StudyType
     */
    private $type;

    /**
     * @ORM\Column(type="MethodType", name="method_type")
     *
     * @var MethodType
     */
    private $methodType;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Terminology\CodedText",cascade={"persist"})
     * @ORM\JoinColumn(name="studied_condition", referencedColumnName="id", nullable=true)
     *
     * @var CodedText|null
     */
    private $condition;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Terminology\CodedText",cascade={"persist"})
     * @ORM\JoinColumn(name="intervention", referencedColumnName="id", nullable=true)
     *
     * @var CodedText|null
     */
    private $intervention;

    /**
     * @ORM\Column(type="RecruitmentStatusType", name="recruitment_status", nullable=true)
     *
     * @var RecruitmentStatus|null
     */
    private $recruitmentStatus;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int|null
     */
    private $estimatedEnrollment;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $consentPublish;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     *
     * @var bool|null
     */
    private $consentSocialMedia;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     *
     * @var DateTimeImmutable|null
     */
    private $estimatedStudyStartDate;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     *
     * @var DateTimeImmutable|null
     */
    private $estimatedStudyCompletionDate;

    /**
     * @ORM\Column(type="date_immutable", nullable=true)
     *
     * @var DateTimeImmutable|null
     */
    private $studyCompletionDate;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="study_contacts")
     *
     * @var Agent[]|ArrayCollection
     */
    private $contacts;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="study_centers")
     *
     * @var Agent[]|ArrayCollection
     */
    private $centers;

    /**
     * @ORM\Column(type="iri", nullable=true)
     *
     * @var Iri|null
     */
    private $logo = null;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime $created
     */
    protected $created;

    /**
     * @ORM\Column(type="datetime", nullable = true)
     *
     * @var DateTime|null $updated
     */
    protected $updated;

    public function __construct(
        string $briefName,
        ?string $scientificName,
        ?string $briefSummary,
        ?string $summary,
        StudyType $type,
        ?CodedText $condition,
        ?CodedText $intervention,
        int $estimatedEnrollment,
        ?DateTimeImmutable $estimatedStudyStartDate,
        ?DateTimeImmutable $estimatedStudyCompletionDate,
        ?RecruitmentStatus $recruitmentStatus,
        MethodType $methodType
    ) {
        $this->briefName = $briefName;
        $this->scientificName = $scientificName;
        $this->briefSummary = $briefSummary;
        $this->summary = $summary;
        $this->type = $type;
        $this->condition = $condition;
        $this->intervention = $intervention;
        $this->estimatedEnrollment = $estimatedEnrollment;
        $this->estimatedStudyStartDate = $estimatedStudyStartDate;
        $this->estimatedStudyCompletionDate = $estimatedStudyCompletionDate;
        $this->recruitmentStatus = $recruitmentStatus;
        $this->methodType = $methodType;
        $this->centers = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(?Study $study): void
    {
        $this->study = $study;
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
     * @return Agent[]|ArrayCollection
     */
    public function getContacts()
    {
        return $this->contacts;
    }

    /**
     * @param Agent[]|ArrayCollection $contacts
     */
    public function setContacts($contacts): void
    {
        $this->contacts = $contacts;
    }

    public function addContact(Agent $contact): void
    {
        $this->contacts[] = $contact;
    }

    /**
     * @return Agent[]|ArrayCollection
     */
    public function getCenters()
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

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
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

    public function getMethodType(): ?MethodType
    {
        return $this->methodType;
    }

    public function setMethodType(?MethodType $methodType): void
    {
        $this->methodType = $methodType;
    }

    /**
     * @ORM\PrePersist
     */
    public function onPrePersist(): void
    {
        $this->created = new DateTime('now');
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate(): void
    {
        $this->updated = new DateTime('now');
    }
}
