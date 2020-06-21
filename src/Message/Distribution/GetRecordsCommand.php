<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\FAIRData\Distribution;

class GetRecordsCommand
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
