<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\PermissionType as Enum;

class PermissionType extends EnumType
{
    protected string $name = 'PermissionType';
    protected string $class = Enum::class;
}
