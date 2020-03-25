<?php

namespace App\Api\Request;

use App\Entity\Enum\StudyType;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class StudyMetadataApiRequest extends SingleApiRequest
{
    /** @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $briefName;

    /** @var string|null
     *
     * @Assert\Type("string")
     */
    private $scientificName;

    /** @var string
     *
     * @Assert\Type("string")
     */
    private $briefSummary;

    /** @var StudyType
     *
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $type;

    /** @var string|null
     *
     * @Assert\Type("string")
     */
    private $condition;

    /** @var string|null
     *
     * @Assert\Type("string")
     */
    private $intervention;

    /** @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $estimatedEnrollment;

    /**
     * @var DateTimeImmutable|null
     *
     * @Assert\Date()
     */
    private $estimatedStudyStartDate;

    /**
     * @var DateTimeImmutable|null
     *
     * @Assert\Date()
     */
    private $estimatedStudyCompletionDate;

    protected function parse(): void
    {
        $this->briefName = $this->getFromData('briefName');
        $this->scientificName = $this->getFromData('scientificName');
        $this->briefSummary = $this->getFromData('briefSummary');
        $this->type = $this->getFromData('type');
        $this->condition = $this->getFromData('condition');
        $this->intervention = $this->getFromData('intervention');
        $this->estimatedEnrollment = (int) $this->getFromData('estimatedEnrollment');
        $this->estimatedStudyStartDate = $this->getFromData('estimatedStudyStartDate');
        $this->estimatedStudyCompletionDate = $this->getFromData('estimatedStudyCompletionDate');
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
     * @return StudyType
     */
    public function getType(): StudyType
    {
        return StudyType::fromString($this->type);
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
        return new DateTimeImmutable($this->estimatedStudyStartDate);
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEstimatedStudyCompletionDate(): ?DateTimeImmutable
    {
        return new DateTimeImmutable($this->estimatedStudyCompletionDate);
    }
}