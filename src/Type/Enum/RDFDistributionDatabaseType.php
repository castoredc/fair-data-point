<?php
declare(strict_types=1);

namespace App\Type\Enum;

use App\Entity\Enum\RDFDistributionDatabaseType as Enum;

class RDFDistributionDatabaseType extends EnumType
{
    protected string $name = 'RDFDistributionDatabaseType';
    protected string $class = Enum::class;
}
