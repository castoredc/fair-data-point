<?php
declare(strict_types=1);

namespace App\Api\Request\Distribution;

use App\Api\Request\SingleApiRequest;
use Symfony\Component\Validator\Constraints as Assert;

class DistributionGenerationLogsFilterApiRequest extends SingleApiRequest
{
    public const DEFAULT_PER_PAGE = 25;

    /** @Assert\Type("integer") */
    private int $perPage;

    /** @Assert\Type("integer") */
    private int $page;

    protected function parse(): void
    {
        $this->perPage = (int) $this->getFromQuery('perPage');
        $this->page = (int) $this->getFromQuery('page');
    }

    public function getPerPage(): int
    {
        return $this->perPage !== 0 ? $this->perPage : self::DEFAULT_PER_PAGE;
    }

    public function getPage(): int
    {
        return $this->page !== 0 ? $this->page : 1;
    }
}
