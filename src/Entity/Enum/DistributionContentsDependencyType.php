<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static institute()
 * @method static static valueNode()
 * @method bool isInstitute()
 * @method bool isValueNode()
 * @inheritDoc
 */
class DistributionContentsDependencyType extends Enum
{
    private const INSTITUTE = 'institute';
    private const VALUE_NODE = 'valueNode';
}
