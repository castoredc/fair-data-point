<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

use function in_array;

/**
 * @method static static float()
 * @method static static double()
 * @method static static decimal()
 * @method static static integer()
 * @method static static dateTime()
 * @method static static date()
 * @method static static time()
 * @method static static gDay()
 * @method static static gMonth()
 * @method static static gYear()
 * @method static static gYearMonth()
 * @method static static gMonthDay()
 * @method static static string()
 * @method static static boolean()
 * @method bool isFloat()
 * @method bool isDouble()
 * @method bool isDecimal()
 * @method bool isInteger()
 * @method bool isDateTime()
 * @method bool isDate()
 * @method bool isTime()
 * @method bool isGDay()
 * @method bool isGMonth()
 * @method bool isGYear()
 * @method bool isGYearMonth()
 * @method bool isGMonthDay()
 * @method bool isString()
 * @method bool isBoolean()
 * @inheritDoc
 */
class XsdDataType extends Enum
{
    // Number
    private const FLOAT = 'float';
    private const DOUBLE = 'double';
    private const DECIMAL = 'decimal';
    private const INTEGER = 'integer';
    private const NUMBER_TYPES = [self::FLOAT, self::DOUBLE, self::DECIMAL, self::INTEGER];

    // Date/Time
    private const DATE_TIME = 'dateTime';
    private const DATE = 'date';
    private const TIME = 'time';
    private const G_DAY = 'gDay';
    private const G_MONTH = 'gMonth';
    private const G_YEAR = 'gYear';
    private const G_YEAR_MONTH = 'gYearMonth';
    private const G_MONTH_DAY = 'gMonthDay';
    private const DATE_TIME_TYPES = [self::DATE_TIME, self::DATE, self::TIME, self::G_DAY, self::G_MONTH, self::G_YEAR, self::G_YEAR_MONTH, self::G_MONTH_DAY];

    // String
    private const STRING = 'string';
    private const STRING_TYPES = [self::STRING];

    // Boolean
    private const BOOLEAN = 'boolean';
    private const BOOLEAN_TYPES = [self::BOOLEAN];

    public function isNumberType(): bool
    {
        return in_array($this->toString(), self::NUMBER_TYPES, true);
    }

    public function isDateTimeType(): bool
    {
        return in_array($this->toString(), self::DATE_TIME_TYPES, true);
    }

    public function isStringType(): bool
    {
        return in_array($this->toString(), self::STRING_TYPES, true);
    }

    public function isBooleanType(): bool
    {
        return in_array($this->toString(), self::BOOLEAN_TYPES, true);
    }
}
