<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\FAIRData\Distribution;

class GetRecordCommand
{
    private Distribution $distribution;

    private string $recordId;

    public function __construct(Distribution $distribution, string $recordId)
    {
        $this->distribution = $distribution;
        $this->recordId = $recordId;
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
