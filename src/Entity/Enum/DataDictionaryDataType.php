<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static number()
 * @method static static date()
 * @method static static time()
 * @method static static dateTime()
 * @method static static string()
 * @method static static optionSingle()
 * @method static static optionMultiple()
 * @method bool isNumber()
 * @method bool isDate()
 * @method bool isTime()
 * @method bool isDateTime()
 * @method bool isString()
 * @method bool isOptionSingle()
 * @method bool isOptionMultiple()
 * @inheritDoc
 */
class DataDictionaryDataType extends Enum
{
    // Number
    public const NUMBER = 'number';

    // Date/Time
    public const DATE = 'date';
    public const TIME = 'time';
    public const DATE_TIME = 'dateTime';

    // String
    public const STRING = 'string';

    // Boolean
    public const OPTION_SINGLE = 'optionSingle';
    public const OPTION_MULTIPLE = 'optionMultiple';
}
