<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\StudySource as Enum;

class StudySource extends EnumType
{
    /** @inheritDoc */
    protected $name = 'StudySource';

    /** @inheritDoc */
    protected $class = Enum::class;
}
