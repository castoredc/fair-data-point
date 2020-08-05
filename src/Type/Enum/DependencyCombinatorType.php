<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\DependencyCombinatorType as Enum;

class DependencyCombinatorType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'DependencyCombinatorType';

    /** @inheritDoc */
    protected $class = Enum::class;
}
