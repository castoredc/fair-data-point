<?php
declare(strict_types=1);

namespace App\Message\Distribution;

use App\Entity\Data\Log\DistributionGenerationLog;

class GetDistributionGenerationRecordLogsCommand
{
    /** @var DistributionGenerationLog */
    private $log;

    /** @var int */
    private $perPage;

    /** @var int */
    private $page;

    public function __construct(DistributionGenerationLog $log, int $perPage, int $page)
    {
        $this->log = $log;
        $this->perPage = $perPage;
        $this->page = $page;
    }

    public function getLog(): DistributionGenerationLog
    {
        return $this->log;
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
