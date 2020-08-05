<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\StudyType as Enum;

class StudyType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'StudyType';

    /** @inheritDoc */
    protected $class = Enum::class;
}
