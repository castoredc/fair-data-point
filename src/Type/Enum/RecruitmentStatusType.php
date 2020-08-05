<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\RecruitmentStatus as Enum;

class RecruitmentStatusType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'RecruitmentStatusType';

    /** @inheritDoc */
    protected $class = Enum::class;
}
