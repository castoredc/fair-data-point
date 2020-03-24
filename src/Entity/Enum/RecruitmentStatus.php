<?php

namespace App\Entity\Enum;

/**
 *      @ method static static notyetrecruiting()
 *      @ method static static recruiting()
 *      @ method static static enrollingbyinvitation()
 *      @ method static static activenotrecruiting()
 *      @ method static static suspended()
 *      @ method static static terminated()
 *      @ method static static completed()
 *      @ method static static withdrawn()
 *      @ method bool isNotyetrecruiting()
 *      @ method bool isRecruiting()
 *      @ method bool isEnrollingbyinvitation()
 *      @ method bool isActivenotrecruiting()
 *      @ method bool isSuspended()
 *      @ method bool isTerminated()
 *      @ method bool isCompleted()
 *      @ method bool isWithdrawn()
 */
class RecruitmentStatus extends Enum
{
    private const NOTYETRECRUITING = 'not_yet_recruiting';
    private const RECRUITING = 'recruiting';
    private const ENROLLINGBYINVITATION = 'enrolling_by_invitation';
    private const ACTIVENOTRECRUITING = 'active_not_recruiting';
    private const SUSPENDED = 'suspended';
    private const TERMINATED = 'terminated';
    private const COMPLETED = 'completed';
    private const WITHDRAWN = 'withdrawn';
}