<?php
/**
 * @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant
 */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static view()
 * @method static static edit()
 * @method static static manage()
 * @method bool isView()
 * @method bool isEdit()
 * @method bool isManage()
 * @inheritDoc
 */
class PermissionType extends Enum
{
    private const VIEW = 'view';
    private const EDIT = 'edit';
    private const MANAGE = 'manage';
}
