<?php
declare(strict_types=1);

namespace App\Command\Dataset;

use App\Entity\FAIRData\Agent\Agent;
use App\Entity\FAIRData\Catalog;

class GetPaginatedDatasetsCommand
{
    private ?Catalog $catalog;

    private ?Agent $agent;

    private ?string $search = null;

    private int $perPage;

    private int $page;

    /** @var string[]|null */
    private ?array $hideCatalogs = null;

    /**
     * @param string[]|null $hideCatalogs
     */
    public function __construct(?Catalog $catalog, ?Agent $agent, ?string $search, ?array $hideCatalogs, int $perPage, int $page)
    {
        $this->catalog = $catalog;
        $this->agent = $agent;
        $this->search = $search;
        $this->hideCatalogs = $hideCatalogs;
        $this->perPage = $perPage;
        $this->page = $page;
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

    /**
     * @return string[]|null
     */
    public function getHideCatalogs(): ?array
    {
        return $this->hideCatalogs;
    }
}
