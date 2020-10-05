<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\StudySource as Enum;

class StudySource extends EnumType
{
    protected string $name = 'StudySource';
    protected string $class = Enum::class;
}
