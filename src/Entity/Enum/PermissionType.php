<?php
/** @phpcs:disable SlevomatCodingStandard.Classes.UnusedPrivateElements.UnusedConstant */
declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * @method static static view()
 * @method static static edit()
 * @method static static manage()
 * @method static static accessData()
 * @method bool isView()
 * @method bool isEdit()
 * @method bool isManage()
 * @method bool isAccessData()
 * @inheritDoc
 */
class PermissionType extends Enum
{
    private const VIEW = 'view';
    private const EDIT = 'edit';
    private const MANAGE = 'manage';
    private const ACCESS_DATA = 'access_data';
}
