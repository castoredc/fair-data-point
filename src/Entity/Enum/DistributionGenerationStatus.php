<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static notUpdated()
 * @method static static success()
 * @method static static error()
 * @method static static partially()
 * @method bool isNotUpdated()
 * @method bool isSuccess()
 * @method bool isError()
 * @method bool isPartially()
 * @inheritDoc
 */
class DistributionGenerationStatus extends Enum
{
    private const NOT_UPDATED = 'not_updated';
    private const SUCCESS = 'success';
    private const ERROR = 'error';
    private const PARTIALLY = 'partially';
}
