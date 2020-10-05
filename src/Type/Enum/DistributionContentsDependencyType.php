<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\DistributionContentsDependencyType as Enum;

class DistributionContentsDependencyType extends EnumType
{
    protected string $name = 'DistributionContentsDependencyType';
    protected string $class = Enum::class;
}
