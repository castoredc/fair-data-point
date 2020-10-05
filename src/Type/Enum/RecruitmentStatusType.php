<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\RecruitmentStatus as Enum;

class RecruitmentStatusType extends EnumType
{
    protected string $name = 'RecruitmentStatusType';
    protected string $class = Enum::class;
}
