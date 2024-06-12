<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\MetadataDisplayPosition as Enum;

class MetadataDisplayPosition extends EnumType
{
    protected string $name = 'MetadataDisplayPosition';
    protected string $class = Enum::class;
}
