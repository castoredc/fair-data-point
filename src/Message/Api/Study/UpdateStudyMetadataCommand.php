<?php

namespace App\Message\Api\Study;

use App\Entity\Enum\StudyType;
use App\Security\CastorUser;
use DateTimeImmutable;

class UpdateStudyMetadataCommand
{
    /** @var string
     */
    private $metadataId;

    /** @var string
     */
    private $briefName;

    /** @var string|null
     */
    private $scientificName;

    /** @var string
     */
    private $briefSummary;

    /** @var string|null
     */
    private $summary;

    /** @var StudyType
     */
    private $type;

    /** @var string|null
     */
    private $condition;

    /** @var string|null
     */
    private $intervention;

    /** @var int
     */
    private $estimatedEnrollment;

    /**
     * @var DateTimeImmutable|null
     */
    private $estimatedStudyStartDate;

    /**
     * @var DateTimeImmutable|null
     */
    private $estimatedStudyCompletionDate;

    /**
     * @var CastorUser
     */
    private $user;

    /**
     * CreateStudyCommand constructor.
     *
     * @param string                 $metadataId
     * @param string                 $briefName
     * @param string|null            $scientificName
     * @param string                 $briefSummary
     * @param string|null            $summary
     * @param StudyType              $type
     * @param string|null            $condition
     * @param string|null            $intervention
     * @param int                    $estimatedEnrollment
     * @param DateTimeImmutable|null $estimatedStudyStartDate
     * @param DateTimeImmutable|null $estimatedStudyCompletionDate
     */
    public function __construct(
        string $metadataId,
        string $briefName,
        ?string $scientificName,
        string $briefSummary,
        ?string $summary,
        StudyType $type,
        ?string $condition,
        ?string $intervention,
        int $estimatedEnrollment,
        ?DateTimeImmutable $estimatedStudyStartDate,
        ?DateTimeImmutable $estimatedStudyCompletionDate,
        CastorUser $user
    ) {
        $this->metadataId = $metadataId;
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
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getMetadataId(): string
    {
        return $this->metadataId;
    }

    /**
     * @return string
     */
    public function getBriefName(): string
    {
        return $this->briefName;
    }

    /**
     * @return string|null
     */
    public function getScientificName(): ?string
    {
        return $this->scientificName;
    }

    /**
     * @return string
     */
    public function getBriefSummary(): string
    {
        return $this->briefSummary;
    }

    /**
     * @return string|null
     */
    public function getSummary(): ?string
    {
        return $this->summary;
    }

    /**
     * @return StudyType
     */
    public function getType(): StudyType
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getCondition(): ?string
    {
        return $this->condition;
    }

    /**
     * @return string|null
     */
    public function getIntervention(): ?string
    {
        return $this->intervention;
    }

    /**
     * @return int
     */
    public function getEstimatedEnrollment(): int
    {
        return $this->estimatedEnrollment;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEstimatedStudyStartDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyStartDate;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEstimatedStudyCompletionDate(): ?DateTimeImmutable
    {
        return $this->estimatedStudyCompletionDate;
    }

    /**
     * @return CastorUser
     */
    public function getUser(): CastorUser
    {
        return $this->user;
    }
}