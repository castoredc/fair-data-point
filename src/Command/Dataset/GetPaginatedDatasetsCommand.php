<?php
declare(strict_types=1);

namespace App\Command\Dataset;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;

class GetPaginatedDatasetsCommand
{
    /** @param string[]|null $hideCatalogs */
    public function __construct(private ?Catalog $catalog, private ?Agent $agent, private ?string $search = null, private ?array $hideCatalogs = null, private int $perPage, private int $page)
    {
    }

    public function getCatalog(): ?Catalog
    {
        return $this->catalog;
    }

    public function getAgent(): ?Agent
    {
        return $this->agent;
    }

    public function getSearch(): ?string
    {
        return $this->search;
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
}
