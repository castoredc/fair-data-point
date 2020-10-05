<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\MethodType as Enum;

class MethodType extends EnumType
{
    protected string $name = 'MethodType';
    protected string $class = Enum::class;
}
