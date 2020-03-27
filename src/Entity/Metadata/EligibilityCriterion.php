<?php
declare(strict_types=1);

namespace App\Entity\Metadata;

use App\Entity\Enum\EligibilityCriterionType;
use App\Entity\Terminology\CodedText;

class EligibilityCriterion
{
    /** @var EligibilityCriterionType */
    private $type;

    /** @var CodedText */
    private $criterion;

    public function __construct(EligibilityCriterionType $type, CodedText $criterion)
    {
        $this->type = $type;
        $this->criterion = $criterion;
    }

    public function getType(): EligibilityCriterionType
    {
        return $this->type;
    }

    public function setType(EligibilityCriterionType $type): void
    {
        $this->type = $type;
    }

    public function getCriterion(): CodedText
    {
        return $this->criterion;
    }

    public function setCriterion(CodedText $criterion): void
    {
        $this->criterion = $criterion;
    }
}
