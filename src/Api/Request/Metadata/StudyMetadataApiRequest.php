<?php
declare(strict_types=1);

namespace App\Api\Request\Metadata;

use App\Api\Request\SingleApiRequest;
use App\Entity\Enum\MethodType;
use App\Entity\Enum\RecruitmentStatus;
use App\Entity\Enum\StudyType;
use App\Entity\FAIRData\LocalizedText;
use App\Entity\FAIRData\LocalizedTextItem;
use App\Entity\Terminology\OntologyConcept;
use App\Validator\Constraints as AppAssert;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

class StudyMetadataApiRequest extends SingleApiRequest
{
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $briefName;

    #[Assert\Type('string')]
    private ?string $scientificName = null;

    #[Assert\Type('string')]
    private string $briefSummary;

    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private string $type;

    #[Assert\Type('string')]
    private ?string $condition = null;

    #[Assert\Type('string')]
    private ?string $intervention = null;

    #[Assert\Type('integer')]
    private ?int $estimatedEnrollment;

    #[Assert\Date]
    private ?string $estimatedStudyStartDate = null;

    #[Assert\Date]
    private ?string $estimatedStudyCompletionDate = null;

    #[Assert\Type('string')]
    private ?string $summary = null;

    #[Assert\Type('string')]
    private ?string $recruitmentStatus = null;

    #[Assert\Type('string')]
    private ?string $methodType = null;

    /** @var mixed[] */
    #[Assert\Type('array')]
    private array $conditions;

    /** @var mixed[]|null */
    #[AppAssert\LocalizedText]
    private ?array $keywords = null;

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
        $this->summary = $this->getFromData('summary');
        $this->recruitmentStatus = $this->getFromData('recruitmentStatus');
        $this->methodType = $this->getFromData('methodType');
        $this->conditions = $this->getFromData('conditions');
        $this->keywords = $this->getFromData('keywords');
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
        return $this->condition !== '' ? $this->condition : null;
    }

    public function getIntervention(): ?string
    {
        return $this->intervention !== '' ? $this->intervention : null;
    }

    public function getEstimatedEnrollment(): ?int
    {
        $parsed = (int) $this->estimatedEnrollment;

        return $parsed > 0 ? $parsed : null;
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

    public function getSummary(): ?string
    {
        return $this->summary !== '' ? $this->summary : null;
    }

    public function getRecruitmentStatus(): ?RecruitmentStatus
    {
        return $this->recruitmentStatus !== null ? RecruitmentStatus::fromString($this->recruitmentStatus) : null;
    }

    public function getMethodType(): ?MethodType
    {
        return $this->methodType !== null ? MethodType::fromString($this->methodType) : null;
    }

    /** @return OntologyConcept[] */
    public function getConditions(): array
    {
        $data = [];

        foreach ($this->conditions as $theme) {
            $data[] = OntologyConcept::fromData($theme);
        }

        return $data;
    }

    public function getKeywords(): ?LocalizedText
    {
        return $this->generateLocalizedText($this->keywords);
    }

    /** @param mixed[] $items */
    protected function generateLocalizedText(?array $items): ?LocalizedText
    {
        if ($items === null) {
            return null;
        }

        $texts = new ArrayCollection();

        foreach ($items as $item) {
            $text = new LocalizedTextItem($item['text']);
            $text->setLanguageCode($item['language']);

            $texts->add($text);
        }

        return new LocalizedText($texts);
    }
}
