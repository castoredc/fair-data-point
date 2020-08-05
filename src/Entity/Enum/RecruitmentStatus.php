<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static notYetRecruiting()
 * @method static static recruiting()
 * @method static static enrollingByInvitation()
 * @method static static activeNotRecruiting()
 * @method static static suspended()
 * @method static static terminated()
 * @method static static completed()
 * @method static static withdrawn()
 * @method bool isNotYetRecruiting()
 * @method bool isRecruiting()
 * @method bool isEnrollingByInvitation()
 * @method bool isActiveNotRecruiting()
 * @method bool isSuspended()
 * @method bool isTerminated()
 * @method bool isCompleted()
 * @method bool isWithdrawn()
 * @inheritDoc
 */
class RecruitmentStatus extends Enum
{
    private const NOT_YET_RECRUITING = 'not_yet_recruiting';
    private const RECRUITING = 'recruiting';
    private const ENROLLING_BY_INVITATION = 'enrolling_by_invitation';
    private const ACTIVE_NOT_RECRUITING = 'active_not_recruiting';
    private const SUSPENDED = 'suspended';
    private const TERMINATED = 'terminated';
    private const COMPLETED = 'completed';
    private const WITHDRAWN = 'withdrawn';
}
