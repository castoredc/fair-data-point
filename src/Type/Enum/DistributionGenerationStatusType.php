<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\DistributionGenerationStatus as Enum;

class DistributionGenerationStatusType extends EnumType
{
    /** @inheritDoc */
    protected $name = 'DistributionGenerationStatusType';

    /** @inheritDoc */
    protected $class = Enum::class;
}
