<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\NameOrigin;

class NameOriginType extends EnumType
{
    protected string $name = 'NameOriginType';
    protected string $class = NameOrigin::class;
}
