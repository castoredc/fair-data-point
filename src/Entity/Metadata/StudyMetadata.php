<?php

namespace App\Entity\Metadata;

use App\Entity\Castor\Study;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\StudyPhase;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Agent;
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
     *
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

    /** @var StudyPhase */
    private $phase;

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

    /** @var RecruitmentStatus */
    private $recruitmentStatus;

    /**
     * @ORM\Column(type="integer")
     *
     * @var int
     */
    private $estimatedEnrollment;

    /** @var int|null */
    private $actualEnrollment;

    /** @var @TODO Add enrollment method */
    private $enrollmentMethod;

    /** @var @TODO Add data capture method */
    private $dataCaptureMethod;

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

    /** @var EligibilityCriterion[]|ArrayCollection */
    private $eligibilityCriteria;

    // private $studyTeam;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="study_contacts")
     *
     * @var Agent[]|ArrayCollection
     */
    private $contacts;

    private $trialIds;

    private $sponsors;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\FAIRData\Agent", cascade={"persist"})
     * @ORM\JoinTable(name="study_centers")
     *
     * @var Agent[]|ArrayCollection
     */
    private $centers;

    /**
     * @var DateTime $created
     *
     * @ORM\Column(type="datetime")
     */
    protected $created;

    /**
     * @var DateTime $updated
     *
     * @ORM\Column(type="datetime", nullable = true)
     */
    protected $updated;

    /**
     * StudyMetadata constructor.
     *
     * @param string                 $briefName
     * @param string|null            $scientificName
     * @param string|null            $briefSummary
     * @param string|null            $summary
     * @param StudyType              $type
     * @param CodedText|null         $condition
     * @param CodedText|null         $intervention
     * @param int                    $estimatedEnrollment
     * @param DateTimeImmutable|null $estimatedStudyStartDate
     * @param DateTimeImmutable|null $estimatedStudyCompletionDate
     */
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
        ?DateTimeImmutable $estimatedStudyCompletionDate
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
        $this->centers = new ArrayCollection();
        $this->contacts = new ArrayCollection();
    }

    /**
     * @return Study|null
     */
    public function getStudy(): ?Study
    {
        return $this->study;
    }

    /**
     * @param Study|null $study
     */
    public function setStudy(?Study $study): void
    {
        $this->study = $study;
    }

    /**
     * @return string
     */
    public function getBriefName(): string
    {
        return $this->briefName;
    }

    /**
     * @param string $briefName
     */
    public function setBriefName(string $briefName): void
    {
        $this->briefName = $briefName;
    }

    /**
     * @return string|null
     */
    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    /**
     * @param string|null $scientificName
     */
    public function setScientificName(?string $scientificName): void
    {
        $this->scientificName = $scientificName;
    }

    /**
     * @return string|null
     */
    public function getBriefSummary(): ?string
    {
        return $this->briefSummary;
    }

    /**
     * @param string|null $briefSummary
     */
    public function setBriefSummary(?string $briefSummary): void
    {
        $this->briefSummary = $briefSummary;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @param string|null $summary
     */
    public function setSummary(?string $summary): void
    {
        $this->summary = $summary;
    }

    /**
     * @return StudyType
     */
    public function getType(): StudyType
    {
        return $this->type;
    }

    /**
     * @param StudyType $type
     */
    public function setType(StudyType $type): void
    {
        $this->type = $type;
    }

    /**
     * @return StudyPhase
     */
    public function getPhase(): StudyPhase
    {
        return $this->phase;
    }

    /**
     * @param StudyPhase $phase
     */
    public function setPhase(StudyPhase $phase): void
    {
        $this->phase = $phase;
    }

    /**
     * @return CodedText|null
     */
    public function getCondition(): ?CodedText
    {
        return $this->condition;
    }

    /**
     * @param CodedText|null $condition
     */
    public function setCondition(?CodedText $condition): void
    {
        $this->condition = $condition;
    }

    /**
     * @return CodedText|null
     */
    public function getIntervention(): ?CodedText
    {
        return $this->intervention;
    }

    /**
     * @param CodedText|null $intervention
     */
    public function setIntervention(?CodedText $intervention): void
    {
        $this->intervention = $intervention;
    }

    /**
     * @return RecruitmentStatus
     */
    public function getRecruitmentStatus(): RecruitmentStatus
    {
        return $this->recruitmentStatus;
    }

    /**
     * @param RecruitmentStatus $recruitmentStatus
     */
    public function setRecruitmentStatus(RecruitmentStatus $recruitmentStatus): void
    {
        $this->recruitmentStatus = $recruitmentStatus;
    }

    /**
     * @return int
     */
    public function getEstimatedEnrollment(): int
    {
        return $this->estimatedEnrollment;
    }

    /**
     * @param int $estimatedEnrollment
     */
    public function setEstimatedEnrollment(int $estimatedEnrollment): void
    {
        $this->estimatedEnrollment = $estimatedEnrollment;
    }

    /**
     * @return int|null
     */
    public function getActualEnrollment(): ?int
    {
        return $this->actualEnrollment;
    }

    /**
     * @param int|null $actualEnrollment
     */
    public function setActualEnrollment(?int $actualEnrollment): void
    {
        $this->actualEnrollment = $actualEnrollment;
    }

    /**
     * @return mixed
     */
    public function getEnrollmentMethod()
    {
        return $this->enrollmentMethod;
    }

    /**
     * @param mixed $enrollmentMethod
     */
    public function setEnrollmentMethod($enrollmentMethod): void
    {
        $this->enrollmentMethod = $enrollmentMethod;
    }

    /**
     * @return mixed
     */
    public function getDataCaptureMethod()
    {
        return $this->dataCaptureMethod;
    }

    /**
     * @param mixed $dataCaptureMethod
     */
    public function setDataCaptureMethod($dataCaptureMethod): void
    {
        $this->dataCaptureMethod = $dataCaptureMethod;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEstimatedStudyStartDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyStartDate;
    }

    /**
     * @param DateTimeImmutable|null $estimatedStudyStartDate
     */
    public function setEstimatedStudyStartDate(?DateTimeImmutable $estimatedStudyStartDate): void
    {
        $this->estimatedStudyStartDate = $estimatedStudyStartDate;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEstimatedStudyCompletionDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyCompletionDate;
    }

    /**
     * @param DateTimeImmutable|null $estimatedStudyCompletionDate
     */
    public function setEstimatedStudyCompletionDate(?DateTimeImmutable $estimatedStudyCompletionDate): void
    {
        $this->estimatedStudyCompletionDate = $estimatedStudyCompletionDate;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStudyCompletionDate(): ?DateTimeImmutable
    {
        return $this->studyCompletionDate;
    }

    /**
     * @param DateTimeImmutable|null $studyCompletionDate
     */
    public function setStudyCompletionDate(?DateTimeImmutable $studyCompletionDate): void
    {
        $this->studyCompletionDate = $studyCompletionDate;
    }

    /**
     * @return EligibilityCriterion[]|ArrayCollection
     */
    public function getEligibilityCriteria()
    {
        return $this->eligibilityCriteria;
    }

    /**
     * @param EligibilityCriterion[]|ArrayCollection $eligibilityCriteria
     */
    public function setEligibilityCriteria($eligibilityCriteria): void
    {
        $this->eligibilityCriteria = $eligibilityCriteria;
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

    /**
     * @param Agent $contact
     *
     * @return void
     */
    public function addContact(Agent $contact): void
    {
        $this->contacts[] = $contact;
    }

    /**
     * @return mixed
     */
    public function getTrialIds()
    {
        return $this->trialIds;
    }

    /**
     * @param mixed $trialIds
     */
    public function setTrialIds($trialIds): void
    {
        $this->trialIds = $trialIds;
    }

    /**
     * @return mixed
     */
    public function getSponsors()
    {
        return $this->sponsors;
    }

    /**
     * @param mixed $sponsors
     */
    public function setSponsors($sponsors): void
    {
        $this->sponsors = $sponsors;
    }

    /**
     * @return Agent[]|ArrayCollection
     */
    public function getCenters()
    {
        return $this->centers;
    }

    /**
     * @param Agent[]|ArrayCollection $centers
     */
    public function setCenters($centers): void
    {
        $this->centers = $centers;
    }

    /**
     * @param Agent $center
     *
     * @return void
     */
    public function addCenter(Agent $center): void
    {
        $this->centers[] = $center;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }


    /**
     * @ORM\PrePersist
     */
    public function onPrePersist()
    {
        $this->created = new DateTime("now");
    }

    /**
     * @ORM\PreUpdate
     */
    public function onPreUpdate()
    {
        $this->updated = new DateTime("now");
    }
}