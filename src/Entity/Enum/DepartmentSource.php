<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static database()
 * @method static static manual()
 * @method bool isDatabase()
 * @method bool isManual()
 * @inheritDoc
 */
class DepartmentSource extends Enum
{
    private const DATABASE = 'database';
    private const MANUAL = 'manual';
}
