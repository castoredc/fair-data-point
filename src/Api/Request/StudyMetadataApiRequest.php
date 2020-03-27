<?php
declare(strict_types=1);

namespace App\Api\Request;

use App\Entity\Enum\StudyType;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints as Assert;

class StudyMetadataApiRequest extends SingleApiRequest
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $briefName;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $scientificName;

    /**
     * @var string
     * @Assert\Type("string")
     */
    private $briefSummary;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type("string")
     */
    private $type;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $condition;

    /**
     * @var string|null
     * @Assert\Type("string")
     */
    private $intervention;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type("integer")
     */
    private $estimatedEnrollment;

    /**
     * @var string|null
     * @Assert\Date()
     */
    private $estimatedStudyStartDate;

    /**
     * @var string|null
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

    public function getType(): StudyType
    {
        return StudyType::fromString($this->type);
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
        if ($this->estimatedStudyStartDate === null) {
            return null;
        }

        return new DateTimeImmutable($this->estimatedStudyStartDate);
    }

    public function getEstimatedStudyCompletionDate(): ?DateTimeImmutable
    {
        if ($this->estimatedStudyCompletionDate === null) {
            return null;
        }

        return new DateTimeImmutable($this->estimatedStudyCompletionDate);
    }
}
