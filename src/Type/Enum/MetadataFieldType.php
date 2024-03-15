<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\MetadataFieldType as Enum;

class MetadataFieldType extends EnumType
{
    protected string $name = 'MetadataFieldType';
    protected string $class = Enum::class;
}
