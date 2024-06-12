<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\MetadataDisplayType as Enum;

class MetadataDisplayType extends EnumType
{
    protected string $name = 'MetadataDisplayType';
    protected string $class = Enum::class;
}
