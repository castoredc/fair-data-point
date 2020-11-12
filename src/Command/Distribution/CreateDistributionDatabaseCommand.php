<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\FAIRData\Distribution;

class CreateDistributionDatabaseCommand
{
    private Distribution $distribution;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }
}
