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

    /** @deprecated Stardog support was a proof of concept and is no longer in use. */
    public const STARDOG = 'stardog';
}
