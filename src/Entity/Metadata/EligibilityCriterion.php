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
}
