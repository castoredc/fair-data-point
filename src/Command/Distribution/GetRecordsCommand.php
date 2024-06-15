<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\FAIRData\Distribution;

class GetRecordsCommand
{
    public function __construct(private Distribution $distribution)
    {
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }
}
