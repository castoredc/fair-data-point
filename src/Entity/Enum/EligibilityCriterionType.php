<?php

namespace App\Entity\Enum;

/**
 *      @ method static static inclusion()
 *      @ method static static exclusion()
 *      @ method bool isInclusion()
 *      @ method bool isExclusion()
 */
class EligibilityCriterionType extends Enum
{
    private const INCLUSION = 'inclusion';
    private const EXCLUSION = 'exclusion';
}