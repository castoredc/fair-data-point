<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @ method static static earlyphase1()
 * @ method static static phase1()
 * @ method static static phase2()
 * @ method static static phase3()
 * @ method static static phase4()
 * @ method static static notapplicable()
 * @ method bool isEarlyphase1()
 * @ method bool isPhase1()
 * @ method bool isPhase2()
 * @ method bool isPhase3()
 * @ method bool isPhase4()
 * @ method bool isNotapplicable()
 *
 * @inheritDoc
 */
class StudyPhase extends Enum
{
    private const EARLYPHASE1 = '0';
    private const PHASE1 = '1';
    private const PHASE2 = '2';
    private const PHASE3 = '3';
    private const PHASE4 = '4';
    private const NOTAPPLICABLE = 'NA';
}
