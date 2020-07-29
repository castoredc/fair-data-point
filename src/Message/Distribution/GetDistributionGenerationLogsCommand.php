<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\FAIRData\Distribution;

class GetDistributionGenerationLogsCommand
{
    /** @var Distribution */
    private $distribution;

    /** @var int */
    private $perPage;

    /** @var int */
    private $page;

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
