<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static major()
 * @method static static minor()
 * @method static static patch()
 * @method bool isMajor()
 * @method bool isMinor()
 * @method bool isPatch()
 * @inheritDoc
 */
class VersionType extends Enum
{
    private const MAJOR = 'major';
    private const MINOR = 'minor';
    private const PATCH = 'patch';
}
