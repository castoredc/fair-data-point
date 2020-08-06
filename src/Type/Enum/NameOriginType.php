<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\NameOrigin;

class NameOriginType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'NameOriginType';
    /** @inheritDoc */
    protected $class = NameOrigin::class;
}
