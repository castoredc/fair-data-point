<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static castor()
 * @method static static orcid()
 * @method static static user()
 * @method bool isCastor()
 * @method bool isOrcid()
 * @method bool isUser()
 * @inheritDoc
 */
class NameOrigin extends Enum
{
    private const CASTOR = 'castor';
    private const ORCID = 'orcid';
    private const USER = 'user';
}
