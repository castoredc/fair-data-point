<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\XsdDataType as Enum;

class XsdDataType extends EnumType
{
    protected string $name = 'XsdDataType';
    protected string $class = Enum::class;
}
