<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static and()
 * @method static static or()
 * @method bool isAnd()
 * @method bool isOr()
 * @inheritDoc
 */
class DependencyCombinatorType extends Enum
{
    private const AND = 'and';
    private const OR = 'or';
}
