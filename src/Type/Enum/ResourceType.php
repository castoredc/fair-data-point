<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\ResourceType as Enum;

class ResourceType extends EnumType
{
    protected string $name = 'ResourceType';
    protected string $class = Enum::class;
}
