<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static castor()
 * @method static static other()
 * @method bool isCastor()
 * @method bool isOther()
 * @inheritDoc
 */
class StudySource extends Enum
{
    private const CASTOR = 'castor';
    private const OTHER = 'other';
}
