<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static mysql()
 * @method static static stardog()
 * @method bool isMysql()
 * @method bool isStardog()
 * @inheritDoc
 */
class RDFDistributionDatabaseType extends Enum
{
    public const MYSQL = 'mysql';
    public const STARDOG = 'stardog';
}
