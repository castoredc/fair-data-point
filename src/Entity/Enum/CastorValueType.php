<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static plain()
 * @method static static annotated()
 * @method static static entity()
 * @method bool isPlain()
 * @method bool isAnnotated()
 * @method bool isEntity()
 * @inheritDoc
 */
class CastorValueType extends Enum
{
    private const PLAIN = 'plain';
    private const ANNOTATED = 'annotated';
    private const ENTITY = 'entity';
}
