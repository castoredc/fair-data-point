<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @ method static static inclusion()
 * @ method static static exclusion()
 * @ method bool isInclusion()
 * @ method bool isExclusion()
 *
 * @inheritDoc
 */
class EligibilityCriterionType extends Enum
{
    private const INCLUSION = 'inclusion';
    private const EXCLUSION = 'exclusion';
}
