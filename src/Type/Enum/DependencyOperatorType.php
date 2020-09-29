<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\DependencyOperatorType as Enum;

class DependencyOperatorType extends EnumType
{
    protected string $name = 'DependencyOperatorType';
    protected string $class = Enum::class;
}
