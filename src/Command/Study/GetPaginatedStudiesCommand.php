<?php
declare(strict_types=1);

namespace App\Command\Study;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;

class GetPaginatedStudiesCommand
{
    /** @param string[]|null $hideCatalogs */
    public function __construct(
        private int $perPage,
        private int $page,
        private bool $includeUnpublished,
        private ?Catalog $catalog = null,
        private ?Agent $agent = null,
        private ?array $hideCatalogs = null,
    ) {
    }

    public function getCatalog(): ?Catalog
    {
        return $this->catalog;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    /** @return string[]|null */
    public function getHideCatalogs(): ?array
    {
        return $this->hideCatalogs;
    }

    public function getIncludeUnpublished(): bool
    {
        return $this->includeUnpublished;
    }
}
