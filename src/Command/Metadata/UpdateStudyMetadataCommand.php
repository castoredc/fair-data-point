<?php
declare(strict_types=1);

namespace App\Command\Metadata;

use App\Entity\Enum\MethodType;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\Metadata\StudyMetadata;
use App\Entity\Terminology\OntologyConcept;
use DateTimeImmutable;

class UpdateStudyMetadataCommand
{
    private StudyMetadata $metadata;

    private string $briefName;

    private ?string $scientificName = null;

    private string $briefSummary;

    private ?string $summary = null;

    private StudyType $type;

    private ?string $intervention = null;

    private ?int $estimatedEnrollment;

    private ?DateTimeImmutable $estimatedStudyStartDate = null;

    private ?DateTimeImmutable $estimatedStudyCompletionDate = null;

    private ?RecruitmentStatus $recruitmentStatus = null;

    private ?MethodType $methodType = null;

    /** @var OntologyConcept[] */
    private array $conditions;

    private ?LocalizedText $keywords = null;

    /**
     * @param OntologyConcept[] $conditions
     */
    public function __construct(
        StudyMetadata $metadata,
        string $briefName,
        ?string $scientificName,
        string $briefSummary,
        ?string $summary,
        StudyType $type,
        array $conditions,
        ?string $intervention,
        ?int $estimatedEnrollment,
        ?DateTimeImmutable $estimatedStudyStartDate,
        ?DateTimeImmutable $estimatedStudyCompletionDate,
        ?RecruitmentStatus $recruitmentStatus,
        ?MethodType $methodType,
        ?LocalizedText $keywords
    ) {
        $this->metadata = $metadata;
        $this->briefName = $briefName;
        $this->scientificName = $scientificName;
        $this->briefSummary = $briefSummary;
        $this->summary = $summary;
        $this->type = $type;
        $this->conditions = $conditions;
        $this->intervention = $intervention;
        $this->estimatedEnrollment = $estimatedEnrollment;
        $this->estimatedStudyStartDate = $estimatedStudyStartDate;
        $this->estimatedStudyCompletionDate = $estimatedStudyCompletionDate;
        $this->recruitmentStatus = $recruitmentStatus;
        $this->methodType = $methodType;
        $this->keywords = $keywords;
    }

    public function getMetadata(): StudyMetadata
    {
        return $this->metadata;
    }

    public function getBriefName(): string
    {
        return $this->briefName;
    }

    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    public function getBriefSummary(): string
    {
        return $this->briefSummary;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function getType(): StudyType
    {
        return $this->type;
    }

    /**
     * @return OntologyConcept[]
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    public function getIntervention(): ?string
    {
        return $this->intervention;
    }

    public function getEstimatedEnrollment(): ?int
    {
        return $this->estimatedEnrollment;
    }

    public function getEstimatedStudyStartDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyStartDate;
    }

    public function getEstimatedStudyCompletionDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyCompletionDate;
    }

    public function getRecruitmentStatus(): ?RecruitmentStatus
    {
        return $this->recruitmentStatus;
    }

    public function getMethodType(): ?MethodType
    {
        return $this->methodType;
    }

    public function getKeywords(): ?LocalizedText
    {
        return $this->keywords;
    }
}
