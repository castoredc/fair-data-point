<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @ method static static survey()
 * @ method static static registry()
 * @ method static static rct()
 * @ method static static other()
 * @ method bool isSurvey()
 * @ method bool isRegistry()
 * @ method bool isRct()
 * @ method bool isOther()
 *
 * @inheritDoc
 */
class MethodType extends Enum
{
    private const SURVEY = 'survey';
    private const REGISTRY = 'registry';
    private const RCT = 'rct';
    private const OTHER = 'other';
}
