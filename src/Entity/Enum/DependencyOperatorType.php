<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static null()
 * @method static static notNull()
 * @method static static equal()
 * @method static static notEqual()
 * @method static static smallerThan()
 * @method static static smallerThanOrEqualTo()
 * @method static static greaterThan()
 * @method static static greaterThanOrEqualTo()
 * @method bool isNull()
 * @method bool isNotNull()
 * @method bool isEqual()
 * @method bool isNotEqual()
 * @method bool isSmallerThan()
 * @method bool isSmallerThanOrEqualTo()
 * @method bool isGreaterThan()
 * @method bool isGreaterThanOrEqualTo()
 * @inheritDoc
 */
class DependencyOperatorType extends Enum
{
    private const NULL = 'null';
    private const NOT_NULL = 'notNull';
    private const EQUAL = '=';
    private const NOT_EQUAL = '!=';
    private const SMALLER_THAN = '<';
    private const SMALLER_THAN_OR_EQUAL_TO = '<=';
    private const GREATER_THAN = '>';
    private const GREATER_THAN_OR_EQUAL_TO = '>=';
}
