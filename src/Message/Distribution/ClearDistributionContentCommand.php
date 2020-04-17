<?php

namespace App\Message\Distribution;

use App\Entity\FAIRData\Distribution\Distribution;

class ClearDistributionContentCommand
{
    /** @var Distribution */
    private $distribution;

    public function __construct(Distribution $distribution)
    {
        $this->distribution = $distribution;
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }
}