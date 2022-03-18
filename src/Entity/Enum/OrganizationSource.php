<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static database()
 * @method static static grid()
 * @method static static manual()
 * @method bool isDatabase()
 * @method bool isGrid()
 * @method bool isManual()
 * @inheritDoc
 */
class OrganizationSource extends Enum
{
    private const DATABASE = 'database';
    private const GRID = 'grid';
    private const MANUAL = 'manual';
}
