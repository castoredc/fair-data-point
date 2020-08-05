<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\StructureType as Enum;

class StructureType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'StructureType';

    /** @inheritDoc */
    protected $class = Enum::class;
}
