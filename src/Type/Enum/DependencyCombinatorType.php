<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\DependencyCombinatorType as Enum;

class DependencyCombinatorType extends EnumType
{
    protected string $name = 'DependencyCombinatorType';
    protected string $class = Enum::class;
}
