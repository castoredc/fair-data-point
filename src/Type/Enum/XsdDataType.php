<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\XsdDataType as Enum;

class XsdDataType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'XsdTDataType';

    /** @inheritDoc */
    protected $class = Enum::class;
}
