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
 * @method static static peer()
 * @method bool isCastor()
 * @method bool isOrcid()
 * @method bool isUser()
 * @method bool isPeer()
 * @inheritDoc
 */
class NameOrigin extends Enum
{
    private const CASTOR = 'castor';
    private const ORCID = 'orcid';
    private const USER = 'user';
    private const PEER = 'peer';
}
