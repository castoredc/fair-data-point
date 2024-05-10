<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\FAIRData\Distribution;

class GetRecordCommand
{
    public function __construct(private Distribution $distribution, private string $recordId)
    {
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getRecordId(): string
    {
        return $this->recordId;
    }
}
