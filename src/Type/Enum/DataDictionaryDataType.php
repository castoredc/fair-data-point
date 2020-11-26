<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\DataDictionaryDataType as Enum;

class DataDictionaryDataType extends EnumType
{
    protected string $name = 'DataDictionaryDataType';
    protected string $class = Enum::class;
}
