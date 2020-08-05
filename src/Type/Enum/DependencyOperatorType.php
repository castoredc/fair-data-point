<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\DependencyOperatorType as Enum;

class DependencyOperatorType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'DependencyOperatorType';

    /** @inheritDoc */
    protected $class = Enum::class;
}
