<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\FAIRData\Distribution;

class GetDistributionGenerationLogsCommand
{
    public function __construct(private Distribution $distribution, private int $perPage, private int $page)
    {
    }

    public function getDistribution(): Distribution
    {
        return $this->distribution;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }
}
