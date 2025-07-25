<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\ResourceType;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\Agent\Department;
use App\Entity\FAIRData\Agent\Organization;
use App\Entity\FAIRData\Agent\Person;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\MetadataEnrichedEntity;
use App\Entity\Iri;
use App\Entity\Metadata\StudyMetadata\ParticipatingCenter;
use App\Entity\Metadata\StudyMetadata\StudyTeamMember;
use App\Entity\Study;
use App\Entity\Terminology\CodedText;
use App\Entity\Terminology\OntologyConcept;
use App\Entity\Version;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use function array_merge;

#[ORM\Table(name: 'metadata_study')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class StudyMetadata extends Metadata
{
    #[ORM\JoinColumn(name: 'study_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\ManyToOne(targetEntity: Study::class, inversedBy: 'metadata', cascade: ['persist'])]
    private Study $study;

    #[ORM\Column(type: 'version')]
    private Version $version;

    #[ORM\Column(type: 'string', length: 255)]
    private string $briefName = '';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $scientificName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $briefSummary = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $summary = null;

    #[ORM\Column(type: 'StudyType', name: 'study_type')]
    private StudyType $type;

    #[ORM\Column(type: 'MethodType', name: 'method_type')]
    private MethodType $methodType;

    #[ORM\JoinColumn(name: 'studied_condition', referencedColumnName: 'id', nullable: true)]
    #[ORM\OneToOne(targetEntity: CodedText::class, cascade: ['persist'])]
    private ?CodedText $condition = null;

    #[ORM\JoinColumn(name: 'intervention', referencedColumnName: 'id', nullable: true)]
    #[ORM\OneToOne(targetEntity: CodedText::class, cascade: ['persist'])]
    private ?CodedText $intervention = null;

    #[ORM\Column(type: 'RecruitmentStatusType', name: 'recruitment_status', nullable: true)]
    private ?RecruitmentStatus $recruitmentStatus = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $estimatedEnrollment = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $consentPublish = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $consentSocialMedia = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $estimatedStudyStartDate = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $estimatedStudyCompletionDate = null;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?DateTimeImmutable $studyCompletionDate = null;

    /** @var Collection<StudyTeamMember> */
    #[ORM\OneToMany(targetEntity: StudyTeamMember::class, mappedBy: 'metadata', cascade: ['persist', 'remove'], fetch: 'EAGER')]
    private Collection $studyTeamMembers;

    /** @var Collection<ParticipatingCenter> */
    #[ORM\OneToMany(targetEntity: ParticipatingCenter::class, mappedBy: 'metadata', cascade: ['persist', 'remove'], fetch: 'EAGER')]
    private Collection $participatingCenters;

    /** @var Collection<OntologyConcept> */
    #[ORM\JoinTable(name: 'metadata_study_conditions')]
    #[ORM\ManyToMany(targetEntity: OntologyConcept::class, cascade: ['persist'])]
    private Collection $conditions;

    #[ORM\JoinColumn(name: 'keyword', referencedColumnName: 'id')]
    #[ORM\OneToOne(targetEntity: LocalizedText::class, cascade: ['persist'])]
    private ?LocalizedText $keyword = null;

    #[ORM\Column(type: 'iri', nullable: true)]
    private ?Iri $logo = null;

    public function __construct(Study $study)
    {
        $this->study = $study;
        $this->studyTeamMembers = new ArrayCollection();
        $this->participatingCenters = new ArrayCollection();
    }

    public function getStudy(): Study
    {
        return $this->study;
    }

    public function setStudy(Study $study): void
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

    public function getType(): ?StudyType
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

    /** @return Collection<StudyTeamMember> */
    public function getStudyTeam(): Collection
    {
        return $this->studyTeamMembers;
    }

    /** @param Collection<StudyTeamMember> $studyTeamMembers */
    public function setStudyTeam(Collection $studyTeamMembers): void
    {
        $this->studyTeamMembers = $studyTeamMembers;
    }

    public function addStudyTeamMember(Person $person, bool $isContact): void
    {
        $this->studyTeamMembers->add(new StudyTeamMember($this, $person, $isContact));
    }

    public function removeStudyTeamMember(StudyTeamMember $studyTeamMember): void
    {
        $this->studyTeamMembers->removeElement($studyTeamMember);
    }

    /** @return Collection<ParticipatingCenter> */
    public function getCenters(): Collection
    {
        return $this->participatingCenters;
    }

    /** @return Department[] */
    public function getDepartments(): array
    {
        $departments = [];

        foreach ($this->participatingCenters as $participatingCenter) {
            $departments = array_merge($departments, $participatingCenter->getDepartments()->toArray());
        }

        return $departments;
    }

    /** @return Organization[] */
    public function getOrganizations(): array
    {
        $organizations = [];

        foreach ($this->participatingCenters as $participatingCenter) {
            $organizations[] = $participatingCenter->getOrganization();
        }

        return $organizations;
    }

    /** @param ArrayCollection<ParticipatingCenter> $centers */
    public function setCenters(Collection $centers): void
    {
        $this->participatingCenters = $centers;
    }

    public function addCenter(Organization $center): void
    {
        $this->participatingCenters->add(new ParticipatingCenter($this, $center));
    }

    public function removeParticipatingCenter(ParticipatingCenter $participatingCenter): void
    {
        $this->participatingCenters->removeElement($participatingCenter);
    }

    public function removeParticipatingCenterByOrganization(Organization $organization): void
    {
        $element = null;

        foreach ($this->participatingCenters as $participatingCenter) {
            if ($participatingCenter->getOrganization() === $organization) {
                $this->participatingCenters->removeElement($participatingCenter);
                break;
            }
        }
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

    public function getMethodType(): ?MethodType
    {
        return $this->methodType;
    }

    public function setMethodType(MethodType $methodType): void
    {
        $this->methodType = $methodType;
    }

    /** @return Collection<OntologyConcept> */
    public function getConditions(): Collection
    {
        return $this->conditions;
    }

    /** @param Collection<OntologyConcept> $conditions */
    public function setConditions(Collection $conditions): void
    {
        $this->conditions = $conditions;
    }

    public function addCondition(OntologyConcept $condition): void
    {
        $this->conditions->add($condition);
    }

    public function removeCondition(OntologyConcept $condition): void
    {
        $this->conditions->removeElement($condition);
    }

    public function getKeywords(): ?LocalizedText
    {
        return $this->keyword;
    }

    public function setKeywords(?LocalizedText $keywords): void
    {
        $this->keyword = $keywords;
    }

    public function getEntity(): ?MetadataEnrichedEntity
    {
        return $this->study;
    }

    public function getResourceType(): ResourceType
    {
        return ResourceType::study();
    }

    public function getLegacyVersion(): Version
    {
        return $this->version;
    }

    public function setLegacyVersion(Version $version): void
    {
        $this->version = $version;
    }
}
