<?php
declare(strict_types=1);

namespace App\Command\Distribution;

use App\Entity\FAIRData\Distribution;

class GetDistributionGenerationLogsCommand
{
    private Distribution $distribution;

    private int $perPage;

    private int $page;

    public function __construct(Distribution $distribution, int $perPage, int $page)
    {
        $this->distribution = $distribution;
        $this->perPage = $perPage;
        $this->page = $page;
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
