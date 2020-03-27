<?php
declare(strict_types=1);

namespace App\Message\Api\Study;

use App\Entity\Enum\StudyType;
use App\Entity\Metadata\StudyMetadata;
use App\Security\CastorUser;
use DateTimeImmutable;

class UpdateStudyMetadataCommand
{
    /** @var StudyMetadata */
    private $metadata;

    /** @var string */
    private $briefName;

    /** @var string|null */
    private $scientificName;

    /** @var string */
    private $briefSummary;

    /** @var string|null */
    private $summary;

    /** @var StudyType */
    private $type;

    /** @var string|null */
    private $condition;

    /** @var string|null */
    private $intervention;

    /** @var int */
    private $estimatedEnrollment;

    /** @var DateTimeImmutable|null */
    private $estimatedStudyStartDate;

    /** @var DateTimeImmutable|null */
    private $estimatedStudyCompletionDate;

    /** @var CastorUser */
    private $user;

    public function __construct(
        StudyMetadata $metadata,
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
        $this->metadata = $metadata;
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

    public function getCondition(): ?string
    {
        return $this->condition;
    }

    public function getIntervention(): ?string
    {
        return $this->intervention;
    }

    public function getEstimatedEnrollment(): int
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

    public function getUser(): CastorUser
    {
        return $this->user;
    }
}
