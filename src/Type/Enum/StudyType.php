<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\StudyType as Enum;

class StudyType extends EnumType
{
    protected string $name = 'StudyType';
    protected string $class = Enum::class;
}
