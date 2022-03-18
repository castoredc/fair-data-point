<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static node()
 * @method static static module()
 * @method bool isNode()
 * @method bool isModule()
 * @inheritDoc
 */
class DataModelMappingType extends Enum
{
    private const NODE = 'node';
    private const MODULE = 'module';
}
