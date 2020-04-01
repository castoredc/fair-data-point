<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @ method static static interventional()
 * @ method static static observational()
 * @ method bool isInterventional()
 * @ method bool isObservational()
 *
 * @inheritDoc
 */
class StudyType extends Enum
{
    private const INTERVENTIONAL = 'interventional';
    private const OBSERVATIONAL = 'observational';
}
